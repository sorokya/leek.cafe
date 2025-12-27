<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property \App\Models\MediaType $id
 * @property string $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaTypeModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaTypeModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaTypeModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaTypeModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaTypeModel whereType($value)
 * @mixin \Eloquent
 */
class MediaTypeModel extends Model
{
    protected $table = 'media_types';

    protected $fillable = [
        'type',
    ];

    protected function casts(): array
    {
        return [
            'id' => MediaType::class,
        ];
    }

    /** @return HasMany<Media, $this> */
    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'media_type_id');
    }
}
