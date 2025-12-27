<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property \App\Models\ContentType $id
 * @property string $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Content> $contents
 * @property-read int|null $contents_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTypeModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTypeModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTypeModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTypeModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTypeModel whereType($value)
 * @mixin \Eloquent
 */
class ContentTypeModel extends Model
{
    public $timestamps = false;

    protected $table = 'content_types';

    protected $fillable = [
        'type',
    ];

    protected function casts(): array
    {
        return [
            'id' => ContentType::class,
        ];
    }

    /** @return HasMany<Content, $this> */
    public function contents(): HasMany
    {
        return $this->hasMany(Content::class, 'content_type_id');
    }
}
