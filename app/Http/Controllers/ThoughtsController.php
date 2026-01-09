<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\ContentType;
use App\Http\Requests\StoreThoughtRequest;
use App\Http\Requests\UpdateThoughtRequest;
use App\Models\Content;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class ThoughtsController extends ContentController
{
    /**
     * @return Builder<Content>
     */
    protected function getListingQuery(): Builder
    {
        return Content::query()
            ->with('user', 'embedImages', 'createdTimeZone')
            ->whereHas('thought')->latest()
            ->visibleForIndex(Auth::user());
    }

    /**
     * @return Builder<Content>
     */
    protected function getShowQuery(): Builder
    {
        return Content::query()
            ->with('user', 'embedImages', 'createdTimeZone')
            ->whereHas('thought');
    }

    protected function getViewName(string $action): string
    {
        return 'thoughts.' . $action;
    }

    protected function getRouteName(string $action): string
    {
        if ($action === 'edit') {
            return 'thoughts.index';
        }

        return 'thoughts.' . $action;
    }

    public function store(StoreThoughtRequest $request): RedirectResponse
    {
        $this->storeFromRequest($request);

        return to_route('thoughts.index');
    }

    public function update(UpdateThoughtRequest $request, string $slug): Response|RedirectResponse
    {
        if ($request->expectsJson() || $request->header('X-Requested-With') === 'fetch') {
            $this->updateContentFromRequest($request, $slug);

            return response()->noContent();
        }

        return $this->updateFromRequest($request, $slug);
    }

    public function editFragment(string $slug): View
    {
        $content = $this->getShowQuery()
            ->with('user', 'embedImages', 'createdTimeZone')
            ->where('slug', $slug)
            ->visibleForShow(Auth::user())
            ->first();

        abort_unless($content instanceof Content, 404);

        return view('thoughts._edit-form', [
            'content' => $content,
        ]);
    }

    public function viewFragment(Request $request, string $slug): Response
    {
        $content = $this->getShowQuery()
            ->with('user', 'embedImages', 'createdTimeZone')
            ->where('slug', $slug)
            ->visibleForShow(Auth::user())
            ->first();

        abort_unless($content instanceof Content, 404);

        return response()->view('thoughts._view', [
            'content' => $content,
            'wrapContent' => $request->boolean('wrapContent'),
        ]);
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

    protected function getContentType(): ContentType
    {
        return ContentType::THOUGHT;
    }
}
