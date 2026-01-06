<?php

declare(strict_types=1);

namespace App\Support;

final class MetricValueFormatter
{
    public static function format(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return self::trimTrailingZeros((string) $value);
        }

        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);
        if ($value === '') {
            return null;
        }

        return self::trimTrailingZeros($value);
    }

    private static function trimTrailingZeros(string $value): string
    {
        // Only normalize simple decimal strings; leave anything else alone.
        if (preg_match('/^-?\d+(?:\.\d+)?$/', $value) !== 1) {
            return $value;
        }

        if (! str_contains($value, '.')) {
            return $value;
        }

        $value = rtrim($value, '0');

        return rtrim($value, '.');
    }
}
