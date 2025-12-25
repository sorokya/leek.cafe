<?php

namespace App\Http\Controllers;

use App\Models\Post;
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
        $post = Post::query()
            ->where('slug', $slug)
            ->whereNotNull('published_at')
            ->with('user')
            ->first();
        if (!$post) {
            abort(404);
        }

        return view('post.show', [
            'post' => $post,
            'renderedBody' => (string) $renderer->render($post->body),
        ]);
    }

    public function edit(Request $request, string $slug): View
    {
        $post = Post::query()
            ->where('slug', $slug)
            ->with('user')
            ->first();
        if (!$post) {
            abort(404);
        }

        $userTimezone = $this->resolveUserTimezone($request);
        $publishedAtLocal = $post->published_at?->copy()->setTimezone($userTimezone)->format('Y-m-d\\TH:i');

        return view('post.edit', [
            'post' => $post,
            'publishedAtLocal' => $publishedAtLocal,
        ]);
    }

    public function update(Request $request, string $slug): RedirectResponse
    {
        $post = Post::query()
            ->where('slug', $slug)
            ->with('user')
            ->first();
        if (!$post) {
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

        $post->title = $validated['title'];
        $post->body = $validated['body'];
        $post->published_at = $publishedAtUtc;
        $post->save();

        return redirect()->route('posts.edit', ['slug' => $post->slug])
            ->with('status', 'Post updated successfully.');
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
