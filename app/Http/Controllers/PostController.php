<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\ContentType;
use App\Models\Image;
use App\Services\ImageUploader;
use App\Services\PostRenderer;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PostController extends Controller
{
    public function show(string $slug, PostRenderer $renderer): View
    {
        $content = Content::query()
            ->where('content_type_id', ContentType::Post->value)
            ->where('slug', $slug)
            ->whereNotNull('published_at')
            ->with('user')
            ->first();
        if (!$content || !$content->body) {
            abort(404);
        }

        return view('post.show', [
            'content' => $content,
            'renderedBody' => (string) $renderer->render($content->body),
        ]);
    }

    public function edit(Request $request, string $slug): View
    {
        $content = Content::query()
            ->where('content_type_id', ContentType::Post->value)
            ->with('user')
            ->where('slug', $slug)
            ->first();
        if (!$content) {
            abort(404);
        }

        $userTimezone = $this->resolveUserTimezone($request);
        $publishedAtLocal = $content->published_at?->copy()->setTimezone($userTimezone)->format('Y-m-d\\TH:i');

        return view('post.edit', [
            'content' => $content,
            'publishedAtLocal' => $publishedAtLocal,
        ]);
    }

    public function update(Request $request, string $slug): RedirectResponse
    {
        $content = Content::query()
            ->where('content_type_id', ContentType::Post->value)
            ->where('slug', $slug)
            ->with('user')
            ->first();
        if (!$content) {
            abort(404);
        }

        if ($request->input('published_at') === '') {
            $request->merge(['published_at' => null]);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'published_at' => ['nullable', 'date_format:Y-m-d\\TH:i'],
        ]);

        $publishedAtUtc = null;
        if ($validated['published_at'] !== null) {
            $userTimezone = $this->resolveUserTimezone($request);

            $publishedAtUtc = Carbon::parse($validated['published_at'], $userTimezone)
                ->setTimezone('UTC');
        }

        $content->title = $validated['title'];
        $content->body = $validated['body'];
        $content->published_at = $publishedAtUtc;
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

    private function resolveUserTimezone(Request $request): string
    {
        $timezone = $request->user()?->timezone;
        if (is_string($timezone) && $timezone !== '' && in_array($timezone, DateTimeZone::listIdentifiers(), true)) {
            return $timezone;
        }

        return (string) config('app.timezone', 'UTC');
    }
}
