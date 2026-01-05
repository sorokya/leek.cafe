<?php

declare(strict_types=1);

namespace App\Models;

use App\ContentType;
use App\ImageRole;
use App\Services\ContentExcerptGenerator;
use App\Visibility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property int $content_type
 * @property string|null $body
 * @property int $visibility
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $coverImage
 * @property-read int|null $cover_image_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $inlineImages
 * @property-read int|null $inline_images_count
 * @property-read \App\Models\Post|null $post
 * @property-read \App\Models\Thought|null $thought
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $thumbnailImage
 * @property-read int|null $thumbnail_image_count
 * @property-read \App\Models\User $user
 *
 * @method static \Database\Factories\ContentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content whereContentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content whereVisibility($value)
 *
 * @mixin \Eloquent
 */
final class Content extends Model implements Feedable
{
    /** @use HasFactory<\Database\Factories\ContentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slug',
        'content_type',
        'title',
        'body',
        'visibility',
    ];

    protected function casts(): array
    {
        return [
            'content_type' => ContentType::class,
            'visibility' => Visibility::class,
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsToMany<Image, $this> */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'content_images');
    }

    /** @return HasOne<Post, $this> */
    public function post(): HasOne
    {
        return $this->hasOne(Post::class, 'content_id');
    }

    /** @return HasOne<Thought, $this> */
    public function thought(): HasOne
    {
        return $this->hasOne(Thought::class, 'content_id');
    }

    /** @return HasOne<Project, $this> */
    public function project(): HasOne
    {
        return $this->hasOne(Project::class, 'content_id');
    }

    /** @return BelongsToMany<Image, $this> */
    public function inlineImages(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'content_images')
            ->wherePivot('role', ImageRole::INLINE->value);
    }

    /** @return BelongsToMany<Image, $this> */
    public function embedImages(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'content_images')
            ->wherePivot('role', ImageRole::EMBED->value);
    }

    /** @return BelongsToMany<Image, $this> */
    public function thumbnailImage(): BelongsToMany
    {
        return $this->images()
            ->wherePivot('role', ImageRole::THUMBNAIL->value);
    }

    /** @return BelongsToMany<Image, $this> */
    public function coverImage(): BelongsToMany
    {
        return $this->images()
            ->wherePivot('role', ImageRole::COVER->value);
    }

    /** @return Collection<int, FeedItem> */
    public static function getFeedItems(): Collection
    {
        return self::query()
            ->with('user')
            ->whereHas('post')
            ->public()
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->map(fn (Content $content): \Spatie\Feed\FeedItem => $content->toFeedItem());
    }

    public function toFeedItem(): FeedItem
    {
        $summary = resolve(ContentExcerptGenerator::class)->generate($this->body ?? '');

        return FeedItem::create([
            'id' => $this->id,
            'title' => $this->title,
            'summary' => $summary,
            'updated' => $this->updated_at,
            'link' => url('/posts/' . $this->slug),
            'authorName' => $this->user->name,
        ]);
    }

    /**
     * Scope a query to only include public content.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Content> $query
     *
     * @return \Illuminate\Database\Eloquent\Builder<Content>
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function public(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('visibility', Visibility::PUBLIC->value);
    }

    /**
     * Scope a query to exclude private content when not authenticated.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Content> $query
     *
     * @return \Illuminate\Database\Eloquent\Builder<Content>
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function visibleToGuests(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('visibility', '!=', Visibility::PRIVATE->value);
    }
}
