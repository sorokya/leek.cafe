<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

enum ContentType: int
{
    case Post = 1;
    // Future content types can be added here
}

/**
 * @property \App\Models\ContentType $id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Content> $content
 * @property-read int|null $content_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTypeModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTypeModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentTypeModel query()
 * @mixin \Eloquent
 */
class ContentTypeModel extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'type',
    ];

    public function casts(): array
    {
        return [
            'id' => ContentType::class,
        ];
    }

    /** @return HasMany<Content, $this> */
    public function content(): HasMany
    {
        return $this->hasMany(Content::class);
    }
}
