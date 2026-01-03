<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ContentRequest;
use App\ImageRole;
use App\Models\Content;
use App\Models\User;
use App\Services\ContentExcerptGenerator;
use App\Services\ContentRenderer;
use App\Services\ImageUploader;
use App\Services\InlineImageSyncer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Str;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class ContentController extends Controller
{
    public function __construct(
        protected readonly ContentRenderer $renderer,
        protected readonly ContentExcerptGenerator $excerptGenerator,
        protected readonly InlineImageSyncer $inlineImageSyncer,
        protected readonly ImageUploader $imageUploader,
    ) {}

    /**
     * Get the base query for listing content.
     *
     * @return Builder<Content>
     */
    abstract protected function getListingQuery(): Builder;

    /**
     * Get the base query for showing content.
     *
     * @return Builder<Content>
     */
    abstract protected function getShowQuery(): Builder;

    /**
     * Get the view name for the given action.
     */
    abstract protected function getViewName(string $action): string;

    /**
     * Get the route name for the given action.
     */
    abstract protected function getRouteName(string $action): string;

    /**
     * Store type-specific data after creating content.
     *
     * @param array<string, mixed> $validated
     */
    abstract protected function storeTypeSpecificData(Content $content, array $validated): void;

    /**
     * Update type-specific data after updating content.
     *
     * @param array<string, mixed> $validated
     */
    abstract protected function updateTypeSpecificData(Content $content, array $validated): void;

    public function index(): View
    {
        $contents = $this->getListingQuery()->paginate(10);
        $contents->getCollection()->transform(function (Content $content): Content {
            $content->setAttribute(
                'excerpt',
                $content->body ? $this->excerptGenerator->generate($content->body) : null,
            );

            return $content;
        });

        $viewName = $this->getViewName('index');
        abort_unless(view()->exists($viewName), 500);

        return view($viewName, [
            'contents' => $contents,
        ]);
    }

    public function show(string $slug): View
    {
        $content = $this->getShowQuery()
            ->where('slug', $slug)
            ->unless(Auth::check(), fn ($q) => $q->visibleToGuests())
            ->first();

        abort_if(! $content || ! $content->body, 404);

        $viewName = $this->getViewName('show');
        abort_unless(view()->exists($viewName), 500);

        return view($viewName, [
            'content' => $content,
            'published_at' => $content->created_at,
            'description' => $this->excerptGenerator->generate($content->body),
            'renderedBody' => (string) $this->renderer->render($content->body),
        ]);
    }

    /**
     * Get additional relationships to eager load for edit and update.
     *
     * @return array<int, string>
     */
    protected function getAdditionalRelationships(): array
    {
        return [];
    }

    public function edit(string $slug): View
    {
        $content = Content::query()
            ->with(array_merge(['user', 'coverImage'], $this->getAdditionalRelationships()))
            ->where('slug', $slug)
            ->first();
        abort_unless($content instanceof Content, 404);

        $viewName = $this->getViewName('edit');
        abort_unless(view()->exists($viewName), 500);

        return view($viewName, [
            'content' => $content,
        ]);
    }

    protected function updateFromRequest(ContentRequest $request, string $slug): RedirectResponse
    {
        $content = Content::query()
            ->with(array_merge(['user'], $this->getAdditionalRelationships()))
            ->where('slug', $slug)
            ->first();
        abort_unless($content instanceof Content, 404);

        $validated = $request->validated();

        $content->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'visibility' => $validated['visibility'],
        ]);

        $this->updateTypeSpecificData($content, $validated);

        if (array_key_exists('cover', $validated) && $validated['cover'] instanceof UploadedFile) {
            $content->coverImage()->detach();
            $img = $this->imageUploader->upload($validated['cover']);
            $content->images()->attach($img->id, ['role' => ImageRole::COVER->value]);
        }

        $this->inlineImageSyncer->sync($content);

        return to_route($this->getRouteName('edit'), ['slug' => $content->slug])
            ->with('status', sprintf('%s updated successfully.', ucfirst($this->getContentType())));
    }

    public function create(): View
    {
        $viewName = $this->getViewName('create');
        abort_unless(view()->exists($viewName), 500);

        return view($viewName);
    }

    protected function storeFromRequest(ContentRequest $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $validated = $request->validated();

        abort_unless(is_string($validated['title']), 400);

        $content = DB::transaction(function () use ($validated, $user) {
            $content = Content::create([
                'user_id' => $user->id,
                'visibility' => $validated['visibility'],
                'title' => $validated['title'],
                'slug' => $validated['slug'] ?? Str::slug($validated['title']),
                'body' => $validated['body'],
            ]);

            $this->storeTypeSpecificData($content, $validated);

            if (array_key_exists('cover', $validated) && $validated['cover'] instanceof UploadedFile) {
                $img = $this->imageUploader->upload($validated['cover']);
                $content->images()->attach($img->id, ['role' => ImageRole::COVER->value]);
            }

            $this->inlineImageSyncer->sync($content);

            return $content;
        });

        return to_route($this->getRouteName('show'), ['slug' => $content->slug]);
    }

    public function deleteConfirm(string $slug): View
    {
        $content = Content::query()
            ->where('slug', $slug)
            ->with('user')
            ->first();
        abort_unless($content instanceof Content, 404);

        $viewName = $this->getViewName('delete-confirm');
        abort_unless(view()->exists($viewName), 500);

        return view($viewName, [
            'content' => $content,
        ]);
    }

    public function destroy(string $slug): RedirectResponse
    {
        $content = Content::query()
            ->where('slug', $slug)
            ->with('user')
            ->first();
        abort_unless($content instanceof Content, 404);

        $content->delete();

        return to_route($this->getRouteName('index'));
    }

    /**
     * Handle image uploads.
     *
     * @return array<string, mixed>
     */
    public function uploadImages(Request $request): array
    {
        $validated = $request->validate([
            'image.*' => ['required', 'image'],
        ]);

        $images = $validated['image'] ?? [];
        $hashes = [];

        foreach ($images as $image) {
            $img = $this->imageUploader->upload($image);
            $hashes[] = $img->getShortHash();
        }

        return ['hashes' => $hashes];
    }

    /**
     * Get the content type name (e.g., 'post', 'project').
     */
    abstract protected function getContentType(): string;
}
