<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\ContentType;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Content;
use App\Queries\PostFeedQuery;
use App\Services\ContentExcerptGenerator;
use App\Services\ContentRenderer;
use App\Services\EmbedImageSyncer;
use App\Services\ImageUploader;
use App\Services\InlineImageSyncer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class PostController extends ContentController
{
    public function __construct(
        private readonly PostFeedQuery $postFeedQuery,
        ContentRenderer $renderer,
        ContentExcerptGenerator $excerptGenerator,
        InlineImageSyncer $inlineImageSyncer,
        EmbedImageSyncer $embedImageSyncer,
        ImageUploader $imageUploader,
    ) {
        parent::__construct($renderer, $excerptGenerator, $inlineImageSyncer, $embedImageSyncer, $imageUploader);
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
        return 'post.' . $action;
    }

    /**
     * Get the route name for the given action.
     */
    protected function getRouteName(string $action): string
    {
        return 'posts.' . $action;
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        return $this->storeFromRequest($request);
    }

    public function update(UpdatePostRequest $request, string $slug): RedirectResponse
    {
        return $this->updateFromRequest($request, $slug);
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
    protected function getContentType(): ContentType
    {
        return ContentType::POST;
    }

    protected function supportsEmbeds(): bool
    {
        return false;
    }
}
