<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property MediaStatus $id
 * @property string $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaStatus whereStatus($value)
 * @mixin \Eloquent
 */
class MediaStatus extends Model
{
    protected $table = 'media_statuses';

    protected $fillable = [
        'status',
    ];

    protected function casts(): array
    {
        return [
            'id' => MediaStatus::class,
        ];
    }

    /** @return HasMany<Media, $this> */
    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'media_status_id');
    }
}
