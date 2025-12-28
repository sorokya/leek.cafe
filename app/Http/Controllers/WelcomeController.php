<?php

namespace App\Http\Controllers;

use App\Queries\PostFeedQuery;
use App\Services\ContentExcerptGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function index(
        PostFeedQuery $postFeedQuery,
        ContentExcerptGenerator $excerptGenerator
    ): View {
        $query = Auth::check()
            ? $postFeedQuery->all()
            : $postFeedQuery->published();

        $content = $query
            ->take(10)
            ->get();

        return view('welcome', ['posts' => array_map(fn($content) => [
            'title' => $content->title,
            'link' => "/posts/{$content->slug}",
            'published_at' => $content->created_at,
            'visibility' => $content->visibility,
            'excerpt' => $content->body ? $excerptGenerator->generate($content->body) : null,
        ], $content->all())]);
    }
}
