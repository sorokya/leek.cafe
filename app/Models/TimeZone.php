<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeZone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeZone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeZone query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeZone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeZone whereName($value)
 *
 * @mixin \Eloquent
 */
final class TimeZone extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
