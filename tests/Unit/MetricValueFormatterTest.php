<?php

declare(strict_types=1);

use App\Support\MetricValueFormatter;

it('formats metric values without unnecessary decimals', function (): void {
    expect(MetricValueFormatter::format('3.0'))->toBe('3');
    expect(MetricValueFormatter::format('3.00'))->toBe('3');
    expect(MetricValueFormatter::format('3.50'))->toBe('3.5');
    expect(MetricValueFormatter::format('3.05'))->toBe('3.05');
    expect(MetricValueFormatter::format('3'))->toBe('3');
});
