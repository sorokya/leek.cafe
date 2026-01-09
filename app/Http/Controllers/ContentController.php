<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\ContentType;
use App\Http\Requests\ContentRequest;
use App\ImageRole;
use App\Models\Content;
use App\Models\TimeZone;
use App\Models\User;
use App\Services\ContentExcerptGenerator;
use App\Services\ContentRenderer;
use App\Services\EmbedImageSyncer;
use App\Services\ImageUploader;
use App\Services\InlineImageSyncer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Str;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class ContentController extends Controller
{
    public function __construct(
        protected readonly ContentRenderer $renderer,
        protected readonly ContentExcerptGenerator $excerptGenerator,
        protected readonly InlineImageSyncer $inlineImageSyncer,
        protected readonly EmbedImageSyncer $embedImageSyncer,
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

    /**
     * Whether this content type supports the "embeds" attachment gallery.
     */
    protected function supportsEmbeds(): bool
    {
        return true;
    }

    public function index(): View
    {
        $contents = $this->getListingQuery()->paginate(10);
        $contents->getCollection()->transform(function (Content $content): Content {
            $content->setAttribute(
                'excerpt',
                $content->rendered ? $this->excerptGenerator->generate($content->rendered) : null,
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
            ->where('slug', 'like', $slug . '%')
            ->visibleForShow(Auth::user())
            ->first();

        abort_if(! $content || ! $content->body, 404);

        $viewName = $this->getViewName('show');
        abort_unless(view()->exists($viewName), 500);

        return view($viewName, [
            'content' => $content,
            'published_at' => $content->createdAtInCreatedTimezone(),
            'description' => $this->excerptGenerator->generate($content->rendered),
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
        $content = $this->getShowQuery()
            ->with(array_merge(['user', 'coverImage', 'createdTimeZone'], $this->getAdditionalRelationships()))
            ->where('slug', $slug)
            ->first();
        abort_unless($content instanceof Content, 404);

        $viewName = $this->getViewName('edit');
        abort_unless(view()->exists($viewName), 500);

        return view($viewName, [
            'content' => $content,
        ]);
    }

    protected function updateContentFromRequest(ContentRequest $request, string $slug): Content
    {
        $content = $this->getShowQuery()
            ->with(array_merge(['user', 'createdTimeZone'], $this->getAdditionalRelationships()))
            ->where('slug', $slug)
            ->first();
        abort_unless($content instanceof Content, 404);

        $validated = $request->validated();

        abort_unless(is_string($validated['body']), 400);

        $content->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'rendered' => $this->renderer->render($validated['body']),
            'visibility' => $validated['visibility'],
        ]);

        $this->updateTypeSpecificData($content, $validated);

        if (array_key_exists('cover', $validated) && $validated['cover'] instanceof UploadedFile) {
            $content->coverImage()->detach();
            $img = $this->imageUploader->upload($validated['cover']);
            $content->images()->attach($img->id, ['role' => ImageRole::COVER->value]);
        }

        $this->inlineImageSyncer->sync($content);

        if ($this->supportsEmbeds()) {
            $embeds = array_key_exists('embeds', $validated) && is_string($validated['embeds'])
                ? $validated['embeds']
                : null;
            $this->embedImageSyncer->sync($content, $embeds);
        } else {
            $content->images()
                ->wherePivot('role', ImageRole::EMBED->value)
                ->detach();
        }

        return $content;
    }

    protected function updateFromRequest(ContentRequest $request, string $slug): RedirectResponse
    {
        $content = $this->updateContentFromRequest($request, $slug);

        return to_route($this->getRouteName('edit'), ['slug' => $content->slug])
            ->with('status', sprintf('%s updated successfully.', ucfirst($this->getContentType()->label())));
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
            $slug = $validated['slug'] ?? Str::slug($validated['title']);

            Validator::validate(
                ['slug' => $slug],
                [
                    'slug' => [
                        'required',
                        'string',
                        'max:255',
                        \Illuminate\Validation\Rule::unique('contents', 'slug')
                            ->where('content_type', $this->getContentType()->value),
                    ],
                ],
            );

            abort_unless(is_string($validated['body']), 400);

            $content = Content::create([
                'user_id' => $user->id,
                'visibility' => $validated['visibility'],
                'title' => $validated['title'],
                'slug' => $slug,
                'content_type' => $this->getContentType()->value,
                'created_timezone_id' => TimeZone::query()->firstOrCreate(['name' => $user->timezone])->id,
                'body' => $validated['body'],
                'rendered' => $this->renderer->render($validated['body']),
            ]);

            $this->storeTypeSpecificData($content, $validated);

            if (array_key_exists('cover', $validated) && $validated['cover'] instanceof UploadedFile) {
                $img = $this->imageUploader->upload($validated['cover']);
                $content->images()->attach($img->id, ['role' => ImageRole::COVER->value]);
            }

            $this->inlineImageSyncer->sync($content);

            if ($this->supportsEmbeds()) {
                $embeds = array_key_exists('embeds', $validated) && is_string($validated['embeds'])
                    ? $validated['embeds']
                    : null;
                $this->embedImageSyncer->sync($content, $embeds);
            }

            return $content;
        });

        return to_route($this->getRouteName('show'), ['slug' => $content->slug]);
    }

    public function deleteConfirm(string $slug): View
    {
        $content = $this->getShowQuery()
            ->where('slug', $slug)
            ->with('user', 'createdTimeZone')
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
        $content = $this->getShowQuery()
            ->where('slug', $slug)
            ->with('user', 'createdTimeZone')
            ->first();
        abort_unless($content instanceof Content, 404);

        $content->delete();

        return to_route($this->getRouteName('index'));
    }

    /**
     * Get the content type name (e.g., 'post', 'project').
     */
    abstract protected function getContentType(): ContentType;
}
