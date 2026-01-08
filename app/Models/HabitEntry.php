<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $habit_id
 * @property \Illuminate\Support\Carbon $date
 * @property bool $done
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Habit $habit
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HabitEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HabitEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HabitEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HabitEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HabitEntry whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HabitEntry whereDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HabitEntry whereHabitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HabitEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HabitEntry whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HabitEntry whereUserId($value)
 *
 * @mixin \Eloquent
 */
final class HabitEntry extends Model
{
    protected $fillable = [
        'user_id',
        'habit_id',
        'date',
        'done',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
            'done' => 'boolean',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Habit, $this> */
    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }
}
