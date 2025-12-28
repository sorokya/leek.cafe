<?php

namespace App\Queries;

use App\Models\Content;
use App\Visibility;
use Illuminate\Database\Eloquent\Builder;

class PostFeedQuery
{
    /** @return Builder<Content> */
    public function published(): Builder
    {
        return Content::query()
            ->with('user', 'post', 'coverImage')
            ->whereHas('post')
            ->where('visibility', Visibility::PUBLIC->value)
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
