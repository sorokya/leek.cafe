<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\PostRenderer;
use Illuminate\Http\Request;
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

    public function edit(string $slug): View
    {
        $post = Post::query()
            ->where('slug', $slug)
            ->with('user')
            ->first();
        if (!$post) {
            abort(404);
        }

        return view('post.edit', [
            'post' => $post,
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

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
        ]);

        $post->title = $validated['title'];
        $post->body = $validated['body'];
        $post->published_at = $validated['published_at'] ?? null;
        $post->save();

        return redirect()->route('posts.edit', ['slug' => $post->slug])
            ->with('status', 'Post updated successfully.');
    }
}
