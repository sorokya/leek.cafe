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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HabitEntry> $entries
 * @property-read int|null $entries_count
 * @property-read \App\Models\User $user
 *
 * @method static \Database\Factories\HabitFactory factory($count = null, $state = [])
 * @method static Builder<static>|Habit newModelQuery()
 * @method static Builder<static>|Habit newQuery()
 * @method static Builder<static>|Habit query()
 * @method static Builder<static>|Habit visibleTo(?\App\Models\User $viewer)
 * @method static Builder<static>|Habit whereColor($value)
 * @method static Builder<static>|Habit whereCreatedAt($value)
 * @method static Builder<static>|Habit whereIcon($value)
 * @method static Builder<static>|Habit whereId($value)
 * @method static Builder<static>|Habit whereName($value)
 * @method static Builder<static>|Habit whereUpdatedAt($value)
 * @method static Builder<static>|Habit whereUserId($value)
 * @method static Builder<static>|Habit whereVisibility($value)
 *
 * @mixin \Eloquent
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
