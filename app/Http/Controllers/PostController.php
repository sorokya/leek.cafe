<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Image;
use App\Queries\PostFeedQuery;
use App\Services\ContentExcerptGenerator;
use App\Services\ImageUploader;
use App\Services\ContentRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PostController extends Controller
{
    public function __construct(
        private PostFeedQuery $postFeedQuery,
        private ContentRenderer $renderer,
        private ContentExcerptGenerator $excerptGenerator,
    ) {}

    public function index(): View
    {
        $query = Auth::check()
            ? $this->postFeedQuery->all()
            : $this->postFeedQuery->published();

        $content = $query
            ->paginate(10);

        return view('post.index', [
            'posts' => array_map(fn($content) => [
                'title' => $content->title,
                'link' => "/posts/{$content->slug}",
                'published_at' => $content->updated_at ?? $content->created_at,
                'visibility' => $content->visibility,
                'excerpt' => $content->body ? $this->excerptGenerator->generate($content->body) : null,
            ], $content->all()),
            'links' => $content->links(),
        ]);
    }

    public function show(string $slug): View
    {
        $query = Auth::check()
            ? $this->postFeedQuery->all()
            : $this->postFeedQuery->published();

        $content = $query
            ->where('slug', $slug)
            ->first();

        if (!$content || !$content->body) {
            abort(404);
        }

        return view('post.show', [
            'content' => $content,
            'published_at' => $content->updated_at ?? $content->created_at,
            'renderedBody' => (string) $this->renderer->render($content->body),
        ]);
    }

    public function edit(string $slug): View
    {
        $content = Content::query()
            ->with('user')
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
        ]);

        $content->title = $validated['title'];
        $content->body = $validated['body'];
        $content->save();

        $imageHashes = $this->extractImageHashes($content->body);
        $imageIds = Image::query()
            ->where(function ($q) use ($imageHashes) {
                foreach ($imageHashes as $prefix) {
                    $q->orWhere('hash', 'like', $prefix . '%');
                }
            })
            ->pluck('id')
            ->all();
        $content->images()->sync($imageIds);

        return redirect()->route('posts.edit', ['slug' => $content->slug])
            ->with('status', 'Post updated successfully.');
    }

    /** Extract image hashes from markdown content.
     * @return array<int, string>
     */
    private function extractImageHashes(string $markdown): array
    {
        preg_match_all('/@img:([a-f0-9]+)/i', $markdown, $matches);

        return array_unique($matches[1]);
    }

    /**
     * Handle image uploads for posts.
     * @return array<string, mixed>
     */
    public function uploadImages(Request $request, ImageUploader $imageUploader): array
    {
        $validated = $request->validate([
            'image.*' => ['required', 'image'],
        ]);

        $images = $validated['image'] ?? [];
        $hashes = [];

        foreach ($images as $image) {
            $hashes[] = $imageUploader->upload($image);
        }

        return ['hashes' => $hashes];
    }
}
