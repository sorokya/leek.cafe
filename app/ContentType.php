<?php

declare(strict_types=1);

namespace App;

enum ContentType: int
{
    case UNKNOWN = 0;
    case POST = 1;
    case PROJECT = 2;
    case THOUGHT = 3;

    public function label(): string
    {
        return match ($this) {
            self::POST => 'post',
            self::PROJECT => 'project',
            self::THOUGHT => 'thought',
            self::UNKNOWN => 'unknown',
        };
    }
}
