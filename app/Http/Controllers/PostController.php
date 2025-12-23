<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\PostRenderer;
use Illuminate\View\View;

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
}
