<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Content;
use App\Queries\PostFeedQuery;
use App\Services\ContentExcerptGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class WelcomeController extends Controller
{
    public function index(
        PostFeedQuery $postFeedQuery,
        ContentExcerptGenerator $excerptGenerator,
    ): View {
        $query = Auth::check()
            ? $postFeedQuery->all()
            : $postFeedQuery->published();

        $contents = $query
            ->take(10)
            ->get();

        $contents->transform(function (Content $content) use ($excerptGenerator): Content {
            $content->setAttribute(
                'excerpt',
                $content->body ? $excerptGenerator->generate($content->body) : null,
            );

            return $content;
        });

        return view('welcome', [
            'contents' => $contents,
        ]);
    }
}
