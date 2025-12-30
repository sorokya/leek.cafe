<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $status
 * @property string $slug
 * @property string|null $icon
 * @property string|null $color
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaStatus whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaStatus whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaStatus whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaStatus whereStatus($value)
 *
 * @mixin \Eloquent
 */
final class MediaStatus extends Model
{
    protected $table = 'media_statuses';

    public $timestamps = false;

    protected $fillable = [
        'status',
        'slug',
        'icon',
        'color',
    ];

    protected function casts(): array
    {
        return [];
    }

    /** @return HasMany<Media, $this> */
    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'media_status_id');
    }
}
