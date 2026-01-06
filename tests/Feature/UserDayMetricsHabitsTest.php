<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Models\HabitEntry;
use App\Models\Metric;
use App\Models\MetricEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\post;

pest()->use(RefreshDatabase::class);

function saveDay(User $user, string $date, array $payload = [], array $headers = []): TestResponse
{
    return post(route('user.day.store', [$user, $date]), $payload, $headers);
}

it('forbids saving another users day', function (): void {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();

    actingAs($attacker);

    $response = saveDay($owner, '2026-01-06', [
        'metrics' => [],
        'habits' => [],
    ]);

    $response->assertForbidden();
});

it('saves option metrics and numeric metrics and habits together', function (): void {
    $user = User::factory()->create(['timezone' => 'UTC']);

    $optionMetric = Metric::factory()->for($user)->create([
        'options' => '1,2,3,4,5,6',
        'min' => null,
        'max' => null,
    ]);

    $numericMetric = Metric::factory()->for($user)->create([
        'options' => null,
        'min' => 0,
        'max' => 10,
    ]);

    $habitA = Habit::factory()->for($user)->create();
    $habitB = Habit::factory()->for($user)->create();

    actingAs($user);

    saveDay($user, '2026-01-06', [
        'metrics' => [
            (string) $optionMetric->id => '3',
            (string) $numericMetric->id => '1.50',
        ],
        'habits' => [
            (string) $habitA->id => '1',
            (string) $habitB->id => '1',
        ],
    ])->assertRedirect();

    assertDatabaseHas(MetricEntry::class, [
        'user_id' => $user->id,
        'metric_id' => $optionMetric->id,
        'date' => '2026-01-06',
        'value' => 3.00,
    ]);

    assertDatabaseHas(MetricEntry::class, [
        'user_id' => $user->id,
        'metric_id' => $numericMetric->id,
        'date' => '2026-01-06',
        'value' => 1.50,
    ]);

    assertDatabaseHas(HabitEntry::class, [
        'user_id' => $user->id,
        'habit_id' => $habitA->id,
        'date' => '2026-01-06',
        'done' => 1,
    ]);

    assertDatabaseHas(HabitEntry::class, [
        'user_id' => $user->id,
        'habit_id' => $habitB->id,
        'date' => '2026-01-06',
        'done' => 1,
    ]);
});

it('deletes entries when values are empty or unchecked', function (): void {
    $user = User::factory()->create(['timezone' => 'UTC']);

    $metric = Metric::factory()->for($user)->create([
        'options' => 'a,b',
    ]);

    $habit = Habit::factory()->for($user)->create();

    MetricEntry::query()->create([
        'user_id' => $user->id,
        'metric_id' => $metric->id,
        'date' => '2026-01-06',
        'value' => 2.00,
    ]);

    HabitEntry::query()->create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'date' => '2026-01-06',
        'done' => true,
    ]);

    actingAs($user);

    saveDay($user, '2026-01-06', [
        'metrics' => [
            (string) $metric->id => '',
        ],
        'habits' => [
            // habit omitted == unchecked
        ],
    ])->assertRedirect();

    assertDatabaseMissing(MetricEntry::class, [
        'user_id' => $user->id,
        'metric_id' => $metric->id,
        'date' => '2026-01-06',
    ]);

    assertDatabaseMissing(HabitEntry::class, [
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'date' => '2026-01-06',
    ]);
});

it('validates option metrics must match configured options', function (): void {
    $user = User::factory()->create(['timezone' => 'UTC']);

    $metric = Metric::factory()->for($user)->create([
        'options' => '1,2,3',
    ]);

    actingAs($user);

    saveDay($user, '2026-01-06', [
        'metrics' => [
            (string) $metric->id => '999',
        ],
    ])->assertSessionHasErrors([
        'metrics.' . $metric->id,
    ]);
});

it('returns no content for fetch json requests', function (): void {
    $user = User::factory()->create(['timezone' => 'UTC']);

    $metric = Metric::factory()->for($user)->create([
        'options' => '1,2,3',
    ]);

    actingAs($user);

    saveDay(
        $user,
        '2026-01-06',
        ['metrics' => [(string) $metric->id => '2']],
        ['Accept' => 'application/json'],
    )->assertNoContent();
});
