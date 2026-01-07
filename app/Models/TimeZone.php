<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 */
final class TimeZone extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
