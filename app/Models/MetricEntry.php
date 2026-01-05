<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $metric_id
 * @property string $date
 * @property string $value
 */
final class MetricEntry extends Model
{
    protected $fillable = [
        'user_id',
        'metric_id',
        'date',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
            'value' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Metric, $this> */
    public function metric(): BelongsTo
    {
        return $this->belongsTo(Metric::class);
    }
}
