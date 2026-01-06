<?php

declare(strict_types=1);

use App\Models\MetricEntry;

it('returns displayValue trimmed of trailing zeros', function (): void {
    $entry = new MetricEntry([
        'value' => '3.00',
    ]);

    expect($entry->displayValue())->toBe('3');
});
