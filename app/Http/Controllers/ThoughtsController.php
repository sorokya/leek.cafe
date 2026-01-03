<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreThoughtRequest;
use App\Http\Requests\UpdateThoughtRequest;
use App\Models\Content;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class ThoughtsController extends ContentController
{
    /**
     * @return Builder<Content>
     */
    protected function getListingQuery(): Builder
    {
        return Content::query()
            ->with('user')
            ->whereHas('thought')->latest()
            ->unless(Auth::check(), fn ($q) => $q->visibleToGuests());
    }

    /**
     * @return Builder<Content>
     */
    protected function getShowQuery(): Builder
    {
        return Content::query()
            ->with('user')
            ->whereHas('thought');
    }

    protected function getViewName(string $action): string
    {
        return 'thoughts.' . $action;
    }

    protected function getRouteName(string $action): string
    {
        if (in_array($action, ['show', 'edit'], true)) {
            return 'thoughts.index';
        }

        return 'thoughts.' . $action;
    }

    public function store(StoreThoughtRequest $request): RedirectResponse
    {
        return $this->storeFromRequest($request);
    }

    public function update(UpdateThoughtRequest $request, string $slug): RedirectResponse
    {
        return $this->updateFromRequest($request, $slug);
    }

    /**
     * @param array<string, mixed> $validated
     */
    protected function storeTypeSpecificData(Content $content, array $validated): void
    {
        $content->thought()->create();
    }

    /**
     * @param array<string, mixed> $validated
     */
    protected function updateTypeSpecificData(Content $content, array $validated): void
    {
        // Thoughts don't have additional data to update
    }

    protected function getContentType(): string
    {
        return 'thought';
    }
}
