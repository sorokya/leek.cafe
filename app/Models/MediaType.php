<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $type
 * @property string $slug
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaType whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaType whereType($value)
 *
 * @mixin \Eloquent
 */
final class MediaType extends Model
{
    protected $table = 'media_types';

    public $timestamps = false;

    protected $fillable = [
        'type',
        'slug',
    ];

    protected function casts(): array
    {
        return [];
    }

    /** @return HasMany<Media, $this> */
    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'media_type_id');
    }
}
