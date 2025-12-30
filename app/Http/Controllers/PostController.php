<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Content;
use App\Queries\PostFeedQuery;
use App\Services\ContentExcerptGenerator;
use App\Services\ContentRenderer;
use App\Services\ImageUploader;
use App\Services\InlineImageSyncer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

final class PostController extends ContentController
{
    public function __construct(
        private readonly PostFeedQuery $postFeedQuery,
        ContentRenderer $renderer,
        ContentExcerptGenerator $excerptGenerator,
        InlineImageSyncer $inlineImageSyncer,
        ImageUploader $imageUploader,
    ) {
        parent::__construct($renderer, $excerptGenerator, $inlineImageSyncer, $imageUploader);
    }

    /**
     * Get the base query for listing content.
     *
     * @return Builder<Content>
     */
    protected function getListingQuery(): Builder
    {
        return Auth::check()
            ? $this->postFeedQuery->all()
            : $this->postFeedQuery->published();
    }

    /**
     * Get the base query for showing content.
     *
     * @return Builder<Content>
     */
    protected function getShowQuery(): Builder
    {
        return Content::query()
            ->with('user', 'coverImage')
            ->whereHas('post');
    }

    /**
     * Get the view name for the given action.
     */
    protected function getViewName(string $action): string
    {
        return "post.{$action}";
    }

    /**
     * Get the route name for the given action.
     */
    protected function getRouteName(string $action): string
    {
        return "posts.{$action}";
    }

    /**
     * Store type-specific data after creating content.
     *
     * @param array<string, mixed> $validated
     */
    protected function storeTypeSpecificData(Content $content, array $validated): void
    {
        $content->post()->create();
    }

    /**
     * Update type-specific data after updating content.
     *
     * @param array<string, mixed> $validated
     */
    protected function updateTypeSpecificData(Content $content, array $validated): void
    {
        // Posts don't have additional data to update
    }

    /**
     * Get the content type name.
     */
    protected function getContentType(): string
    {
        return 'post';
    }
}
