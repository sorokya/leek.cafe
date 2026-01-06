<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $habit_id
 * @property string $date
 * @property bool $done
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
