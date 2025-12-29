<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Content;
use Illuminate\Database\Eloquent\Builder;

final class PostFeedQuery
{
    /** @return Builder<Content> */
    public function published(): Builder
    {
        return Content::query()
            ->with('user', 'post', 'coverImage')
            ->whereHas('post')
            ->public()
            ->orderBy('created_at', 'desc');
    }

    /** @return Builder<Content> */
    public function all(): Builder
    {
        return Content::query()
            ->with('user', 'post', 'coverImage')
            ->whereHas('post')
            ->orderBy('created_at', 'desc');
    }
}
