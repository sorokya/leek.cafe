<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\ImageRole;
use App\Models\Content;
use App\Models\User;
use App\Queries\PostFeedQuery;
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

final class PostController extends Controller
{
    public function __construct(
        private readonly PostFeedQuery $postFeedQuery,
        private readonly ContentRenderer $renderer,
        private readonly ContentExcerptGenerator $excerptGenerator,
        private readonly InlineImageSyncer $inlineImageSyncer,
        private readonly ImageUploader $imageUploader,
    ) {}

    public function index(): View
    {
        $query = Auth::check()
            ? $this->postFeedQuery->all()
            : $this->postFeedQuery->published();

        $contents = $query->paginate(10);
        $contents->getCollection()->transform(function (Content $content): Content {
            $content->setAttribute(
                'excerpt',
                $content->body ? $this->excerptGenerator->generate($content->body) : null,
            );

            return $content;
        });

        return view('post.index', [
            'contents' => $contents,
        ]);
    }

    public function show(string $slug): View
    {
        $content = Content::query()
            ->with('user', 'coverImage')
            ->where('slug', $slug)
            ->whereHas('post')
            ->when(! Auth::check(), fn ($q) => $q->visibleToGuests())
            ->first();

        abort_if(! $content || ! $content->body, 404);

        return view('post.show', [
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
            ->where('slug', $slug)
            ->first();
        abort_unless($content instanceof Content, 404);

        return view('post.edit', [
            'content' => $content,
        ]);
    }

    public function update(UpdatePostRequest $request, string $slug): RedirectResponse
    {
        $content = Content::query()
            ->where('slug', $slug)
            ->with('user')
            ->first();
        abort_unless($content instanceof Content, 404);

        $validated = $request->validated();

        $content->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'visibility' => $validated['visibility'],
        ]);

        // TODO: Add checkbox for deleting current cover image

        if (array_key_exists('cover', $validated) && $validated['cover'] instanceof UploadedFile) {
            $content->coverImage()->detach();
            $img = $this->imageUploader->upload($validated['cover']);
            $content->images()->attach($img->id, ['role' => ImageRole::COVER->value]);
        }

        $this->inlineImageSyncer->sync($content);

        return to_route('posts.edit', ['slug' => $content->slug])
            ->with('status', 'Post updated successfully.');
    }

    public function create(): View
    {
        return view('post.create');
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $validated = $request->validated();

        abort_unless(is_string($validated['title']), 400);

        $content = DB::transaction(function () use ($validated, $user) {
            $content = Content::create([
                'user_id' => $user->id,
                'visibility' => $validated['visibility'],
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']),
                'body' => $validated['body'],
            ]);

            $content->post()->create();

            if ($validated['cover'] instanceof UploadedFile) {
                $img = $this->imageUploader->upload($validated['cover']);
                $content->images()->attach($img->id, ['role' => ImageRole::COVER->value]);
            }

            $this->inlineImageSyncer->sync($content);

            return $content;
        });

        return to_route('posts.show', ['slug' => $content->slug]);
    }

    public function deleteConfirm(string $slug): View
    {
        $content = Content::query()
            ->where('slug', $slug)
            ->with('user')
            ->first();
        abort_unless($content instanceof Content, 404);

        return view('post.delete-confirm', [
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

        return to_route('posts.index');
    }

    /**
     * Handle image uploads for posts.
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
