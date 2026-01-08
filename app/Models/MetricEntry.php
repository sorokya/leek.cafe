<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\MetricValueFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $metric_id
 * @property \Illuminate\Support\Carbon $date
 * @property numeric $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Metric $metric
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MetricEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MetricEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MetricEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MetricEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MetricEntry whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MetricEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MetricEntry whereMetricId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MetricEntry whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MetricEntry whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MetricEntry whereValue($value)
 *
 * @mixin \Eloquent
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

    public function displayValue(): string
    {
        return MetricValueFormatter::format($this->value) ?? '';
    }
}
