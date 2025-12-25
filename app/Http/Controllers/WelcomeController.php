<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\PostRenderer;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function index(PostRenderer $postRenderer): View
    {
        $posts = Post::query()
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->with('user')
            ->with('content')
            ->take(10)
            ->get();

        return view('welcome', ['posts' => array_map(fn($post) => [
            'title' => $post->title,
            'link' => $post->link(),
            'published_at' => $post->published_at,
            'excerpt' => $this->generateExcerpt($post->body, $postRenderer),
        ], $posts->all())]);
    }

    private function generateExcerpt(string $body, PostRenderer $renderer): string
    {
        $rendered = $renderer->render($body);
        $text = strip_tags((string) $rendered);
        if (strlen($text) <= 200) {
            return $text;
        }

        $excerpt = substr($text, 0, 200);
        $lastSpace = strrpos($excerpt, ' ');
        if ($lastSpace !== false) {
            $excerpt = substr($excerpt, 0, $lastSpace);
        }

        return $excerpt . '…';
    }
}
