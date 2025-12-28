<?php

namespace App\Http\Controllers;

use App\ImageRole;
use App\Models\Content;
use App\Models\Image;
use App\Queries\PostFeedQuery;
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

class PostController extends Controller
{
    public function __construct(
        private PostFeedQuery $postFeedQuery,
        private ContentRenderer $renderer,
        private ContentExcerptGenerator $excerptGenerator,
        private InlineImageSyncer $inlineImageSyncer,
        private ImageUploader $imageUploader,
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
                $content->body ? $this->excerptGenerator->generate($content->body) : null
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
            ->when(!Auth::check(), function ($q) {
                $q->where('visibility', '!=', Visibility::PRIVATE->value);
            })
            ->first();

        if (!$content || !$content->body) {
            abort(404);
        }

        return view('post.show', [
            'content' => $content,
            'published_at' => $content->created_at,
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
        if (!$content) {
            abort(404);
        }

        return view('post.edit', [
            'content' => $content,
        ]);
    }

    public function update(Request $request, string $slug): RedirectResponse
    {
        $content = Content::query()
            ->where('slug', $slug)
            ->with('user')
            ->first();
        if (!$content) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'visibility' => ['required', 'integer'],
            'cover' => ['nullable', 'image'],
        ]);

        $content->title = $validated['title'];
        $content->body = $validated['body'];
        $content->visibility = $validated['visibility'];
        $content->save();

        $content->coverImage()->detach();
        if (isset($validated['cover'])) {
            $img = $this->imageUploader->upload($validated['cover']);
            $content->images()->attach($img->id, ['role' => ImageRole::COVER->value]);
        }

        $this->inlineImageSyncer->sync($content);

        return redirect()->route('posts.edit', ['slug' => $content->slug])
            ->with('status', 'Post updated successfully.');
    }

    public function create(): View
    {
        return view('post.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'visibility' => ['required', 'integer'],
            'cover' => ['nullable', 'image'],
        ]);

        $content = new Content();
        $content->user_id = $user->id;
        $content->title = $validated['title'];
        $content->body = $validated['body'];
        $content->slug = Str::slug($validated['title']);
        $content->visibility = $validated['visibility'];
        $content->save();
        $content->post()->create([]);

        if (isset($validated['cover'])) {
            $img = $this->imageUploader->upload($validated['cover']);
            $content->images()->attach($img->id, ['role' => ImageRole::COVER->value]);
        }

        $this->inlineImageSyncer->sync($content);

        return redirect()->route('posts.show', ['slug' => $content->slug]);
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
        if (!$content) {
            abort(404);
        }

        $content->delete();

        return redirect()->route('posts.index');
    }

    /**
     * Handle image uploads for posts.
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
