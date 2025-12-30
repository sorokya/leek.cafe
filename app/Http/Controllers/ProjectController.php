<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Content;
use App\Services\ContentExcerptGenerator;
use App\Services\ContentRenderer;
use App\Services\ImageUploader;
use App\Services\InlineImageSyncer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

final class ProjectController extends ContentController
{
    public function __construct(
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
        $query = Content::query()
            ->with('user', 'project', 'coverImage')
            ->whereHas('project')
            ->when(! Auth::check(), fn ($q) => $q->visibleToGuests());

        return $query;
    }

    /**
     * Get the base query for showing content.
     *
     * @return Builder<Content>
     */
    protected function getShowQuery(): Builder
    {
        return Content::query()
            ->with('user', 'coverImage', 'project')
            ->whereHas('project');
    }

    /**
     * Get the view name for the given action.
     */
    protected function getViewName(string $action): string
    {
        return "project.{$action}";
    }

    /**
     * Get the route name for the given action.
     */
    protected function getRouteName(string $action): string
    {
        return "projects.{$action}";
    }

    /**
     * Store type-specific data after creating content.
     *
     * @param array<string, mixed> $validated
     */
    protected function storeTypeSpecificData(Content $content, array $validated): void
    {
        $content->project()->create([
            'url' => $validated['url'],
        ]);
    }

    /**
     * Update type-specific data after updating content.
     *
     * @param array<string, mixed> $validated
     */
    protected function updateTypeSpecificData(Content $content, array $validated): void
    {
        $content->project()->updateOrCreate(
            [],
            ['url' => $validated['url']],
        );
    }

    /**
     * Get additional relationships to eager load for edit and update.
     *
     * @return array<int, string>
     */
    protected function getAdditionalRelationships(): array
    {
        return ['project'];
    }

    /**
     * Get the content type name.
     */
    protected function getContentType(): string
    {
        return 'project';
    }
}
