<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\ImageRole;
use App\Models\Content;
use App\Services\ContentExcerptGenerator;
use App\Services\ImageUploader;
use App\Services\ContentRenderer;
use App\Services\InlineImageSyncer;
use App\Visibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Str;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class ProjectController extends Controller
{
    public function __construct(
        private ContentRenderer $renderer,
        private ContentExcerptGenerator $excerptGenerator,
        private InlineImageSyncer $inlineImageSyncer,
        private ImageUploader $imageUploader,
    ) {}

    public function index(): View
    {
        $query = Content::query()
            ->with('user', 'project', 'coverImage')
            ->whereHas('project')
            ->when(!Auth::check(), fn($q) => $q->visibleToGuests());

        $contents = $query->paginate(10);
        $contents->getCollection()->transform(function (Content $content): Content {
            $content->setAttribute(
                'excerpt',
                $content->body ? $this->excerptGenerator->generate($content->body) : null
            );

            return $content;
        });

        return view('project.index', [
            'contents' => $contents,
        ]);
    }

    public function show(string $slug): View
    {
        $content = Content::query()
            ->with('user', 'coverImage', 'project')
            ->where('slug', $slug)
            ->whereHas('project')
            ->when(!Auth::check(), fn($q) => $q->visibleToGuests())
            ->first();

        if (!$content || !$content->body) {
            abort(404);
        }

        return view('project.show', [
            'content' => $content,
            'published_at' => $content->created_at,
            'description' => $this->excerptGenerator->generate($content->body),
            'renderedBody' => (string) $this->renderer->render($content->body),
        ]);
    }

    public function edit(string $slug): View
    {
        $content = Content::query()
            ->with('user')
            ->with('coverImage')
            ->with('project')
            ->where('slug', $slug)
            ->first();
        if (!$content) {
            abort(404);
        }

        return view('project.edit', [
            'content' => $content,
        ]);
    }

    public function update(UpdateProjectRequest $request, string $slug): RedirectResponse
    {
        $content = Content::query()
            ->where('slug', $slug)
            ->with('user')
            ->with('project')
            ->first();
        if (!$content) {
            abort(404);
        }

        $validated = $request->validated();

        $content->title = $validated['title'];
        $content->body = $validated['body'];
        $content->visibility = $validated['visibility'];
        $content->save();

        if ($content->project) {
            $content->project->update([
                'url' => $validated['url'],
            ]);
        } else {
            $content->project()->create([
                'url' => $validated['url'],
            ]);
        }

        $content->coverImage()->detach();
        if (isset($validated['cover'])) {
            $img = $this->imageUploader->upload($validated['cover']);
            $content->images()->attach($img->id, ['role' => ImageRole::COVER->value]);
        }

        $this->inlineImageSyncer->sync($content);

        return redirect()->route('projects.edit', ['slug' => $content->slug])
            ->with('status', 'Project updated successfully.');
    }

    public function create(): View
    {
        return view('project.create');
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $validated = $request->validated();

        $content = new Content();
        $content->user_id = $user->id;
        $content->title = $validated['title'];
        $content->body = $validated['body'];
        $content->slug = Str::slug($validated['title']);
        $content->visibility = $validated['visibility'];
        $content->save();
        $content->project()->create([
            'url' => $validated['url'],
        ]);

        if (isset($validated['cover'])) {
            $img = $this->imageUploader->upload($validated['cover']);
            $content->images()->attach($img->id, ['role' => ImageRole::COVER->value]);
        }

        $this->inlineImageSyncer->sync($content);

        return redirect()->route('projects.show', ['slug' => $content->slug]);
    }

    public function deleteConfirm(string $slug): View
    {
        $content = Content::query()
            ->where('slug', $slug)
            ->with('user')
            ->first();
        if (!$content) {
            abort(404);
        }

        return view('project.delete-confirm', [
            'content' => $content,
        ]);
    }

    public function destroy(string $slug): RedirectResponse
    {
        $content = Content::query()
            ->where('slug', $slug)
            ->with('user')
            ->first();
        if (!$content) {
            abort(404);
        }

        $content->delete();

        return redirect()->route('projects.index');
    }

    /**
     * Handle image uploads for projects.
     * @return array<string, mixed>
     */
    public function uploadImages(Request $request): array
    {
        $validated = $request->validate([
            'image.*' => ['required', 'image'],
        ]);

        $images = $validated['image'] ?? [];
        $hashes = [];

        foreach ($images as $image) {
            $img = $this->imageUploader->upload($image);
            $hashes[] = $img->getShortHash();
        }

        return ['hashes' => $hashes];
    }
}
