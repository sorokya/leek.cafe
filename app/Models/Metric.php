<?php

declare(strict_types=1);

namespace App\Models;

use App\Visibility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property Visibility $visibility
 * @property string|null $icon
 * @property string|null $color
 * @property string|null $options
 * @property string|null $min
 * @property string|null $max
 */
final class Metric extends Model
{
    /** @use HasFactory<\Database\Factories\MetricFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'visibility',
        'icon',
        'color',
        'min',
        'max',
        'options',
    ];

    protected function casts(): array
    {
        return [
            'visibility' => Visibility::class,
            'min' => 'decimal:2',
            'max' => 'decimal:2',
        ];
    }

    /**
     * Scope a query for a given viewer.
     *
     * Guests: only PUBLIC.
     * Authenticated: own metrics + PUBLIC from everyone else.
     */
    /**
     * @param Builder<Metric> $query
     *
     * @return Builder<Metric>
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function visibleTo(Builder $query, ?User $viewer): Builder
    {
        if (! $viewer instanceof \App\Models\User) {
            return $query->where('visibility', Visibility::PUBLIC->value);
        }

        return $query->where(function (Builder $q) use ($viewer): void {
            $q
                ->where('visibility', Visibility::PUBLIC->value)
                ->orWhere('user_id', $viewer->id);
        });
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<MetricEntry, $this> */
    public function entries(): HasMany
    {
        return $this->hasMany(MetricEntry::class);
    }

    /** @return list<string> */
    public function optionList(): array
    {
        if (! is_string($this->options) || trim($this->options) === '') {
            return [];
        }

        $parts = array_map(
            trim(...),
            explode(',', $this->options),
        );

        return array_values(array_filter($parts, static fn (string $part): bool => $part !== ''));
    }

    public function hasOptions(): bool
    {
        return $this->optionList() !== [];
    }
}
