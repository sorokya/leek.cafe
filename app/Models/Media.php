<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $content_id
 * @property int $media_type_id
 * @property int $media_status_id
 * @property numeric|null $rating
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $finished_at
 * @property-read \App\Models\Content $content
 * @property-read \App\Models\MediaStatus $mediaStatus
 * @property-read \App\Models\MediaType $mediaType
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereContentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereMediaStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereMediaTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereStartedAt($value)
 *
 * @mixin \Eloquent
 */
final class Media extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'content_id',
        'media_type_id',
        'media_status_id',
        'rating',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    /** @return BelongsTo<Content, $this> */
    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class, 'content_id');
    }

    /** @return BelongsTo<MediaType, $this> */
    public function mediaType(): BelongsTo
    {
        return $this->belongsTo(MediaType::class, 'media_type_id');
    }

    /** @return BelongsTo<MediaStatus, $this> */
    public function mediaStatus(): BelongsTo
    {
        return $this->belongsTo(MediaStatus::class, 'media_status_id');
    }
}
