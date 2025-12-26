<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\ContentType;
use App\Models\Post;
use App\Services\PostRenderer;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function index(PostRenderer $postRenderer): View
    {
        $content = Content::query()
            ->with('user')
            ->where('content_type_id', ContentType::Post->value)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->take(10)
            ->get();

        return view('welcome', ['posts' => array_map(fn($content) => [
            'title' => $content->title,
            'link' => "/posts/{$content->slug}",
            'published_at' => $content->published_at,
            'excerpt' => $content->body ? $this->generateExcerpt($content->body, $postRenderer) : null,
        ], $content->all())]);
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
