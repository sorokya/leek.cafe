<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property \App\Models\MediaStatus $id
 * @property string $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaStatusModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaStatusModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaStatusModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaStatusModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaStatusModel whereStatus($value)
 * @mixin \Eloquent
 */
class MediaStatusModel extends Model
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
