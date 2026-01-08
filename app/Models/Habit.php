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
 */
final class Habit extends Model
{
    /** @use HasFactory<\Database\Factories\HabitFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'visibility',
        'icon',
        'color',
    ];

    protected function casts(): array
    {
        return [
            'visibility' => Visibility::class,
        ];
    }

    /**
     * Scope a query for a given viewer.
     *
     * Guests: only PUBLIC.
     * Authenticated: own habits + PUBLIC from everyone else.
     */
    /**
     * @param Builder<Habit> $query
     *
     * @return Builder<Habit>
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

    /** @return HasMany<HabitEntry, $this> */
    public function entries(): HasMany
    {
        return $this->hasMany(HabitEntry::class);
    }
}
