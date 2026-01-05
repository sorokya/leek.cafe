<?php

declare(strict_types=1);

namespace App\Models;

use App\Visibility;
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
