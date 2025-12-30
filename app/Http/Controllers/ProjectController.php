<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\ImageRole;
use App\Models\Content;
use App\Models\User;
use App\Services\ContentExcerptGenerator;
use App\Services\ContentRenderer;
use App\Services\ImageUploader;
use App\Services\InlineImageSyncer;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Str;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class ProjectController extends Controller
{
    public function __construct(
        private readonly ContentRenderer $renderer,
        private readonly ContentExcerptGenerator $excerptGenerator,
        private readonly InlineImageSyncer $inlineImageSyncer,
        private readonly ImageUploader $imageUploader,
    ) {}

    public function index(): View
    {
        $query = Content::query()
            ->with('user', 'project', 'coverImage')
            ->whereHas('project')
            ->when(! Auth::check(), fn ($q) => $q->visibleToGuests());

        $contents = $query->paginate(10);
        $contents->getCollection()->transform(function (Content $content): Content {
            $content->setAttribute(
                'excerpt',
                $content->body ? $this->excerptGenerator->generate($content->body) : null,
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
            ->when(! Auth::check(), fn ($q) => $q->visibleToGuests())
            ->first();

        abort_if(! $content || ! $content->body, 404);

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
        abort_unless($content instanceof Content, 404);

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
        abort_unless($content instanceof Content, 404);

        $validated = $request->validated();

        $content->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'visibility' => $validated['visibility'],
        ]);

        $content->project()->updateOrCreate(
            [],
            ['url' => $validated['url']],
        );

        if ($validated['cover'] instanceof UploadedFile) {
            $content->coverImage()->detach();
            $img = $this->imageUploader->upload($validated['cover']);
            $content->images()->attach($img->id, ['role' => ImageRole::COVER->value]);
        }

        $this->inlineImageSyncer->sync($content);

        return to_route('projects.edit', ['slug' => $content->slug])
            ->with('status', 'Project updated successfully.');
    }

    public function create(): View
    {
        return view('project.create');
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $validated = $request->validated();

        abort_if(! is_string($validated['title']), 400);

        $content = DB::transaction(function () use ($validated, $user) {
            $content = Content::create([
                'user_id' => $user->id,
                'title' => $validated['title'],
                'body' => $validated['body'],
                'slug' => Str::slug($validated['title']),
                'visibility' => $validated['visibility'],
            ]);

            $content->project()->create([
                'url' => $validated['url'],
            ]);

            if (array_key_exists('cover', $validated) && $validated['cover'] instanceof UploadedFile) {
                $img = $this->imageUploader->upload($validated['cover']);
                $content->images()->attach($img->id, ['role' => ImageRole::COVER->value]);
            }

            $this->inlineImageSyncer->sync($content);

            return $content;
        });

        return to_route('projects.show', ['slug' => $content->slug]);
    }

    public function deleteConfirm(string $slug): View
    {
        $content = Content::query()
            ->where('slug', $slug)
            ->with('user')
            ->first();
        abort_unless($content instanceof Content, 404);

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
        abort_unless($content instanceof Content, 404);

        $content->delete();

        return to_route('projects.index');
    }

    /**
     * Handle image uploads for projects.
     *
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
