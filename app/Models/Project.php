<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $content_id
 * @property string $url
 * @property int|null $image_id
 * @property-read \App\Models\Content|null $content
 * @property-read \App\Models\Image|null $image
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereContentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereUrl($value)
 * @mixin \Eloquent
 */
class Project extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'content_id',
        'url',
        'image_id',
    ];

    /** @return BelongsTo<Content, $this> */
    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class, 'id', 'content_id');
    }

    /** @return BelongsTo<Image, $this> */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'id', 'image_id');
    }
}
