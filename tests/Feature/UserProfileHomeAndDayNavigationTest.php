<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Models\HabitEntry;
use App\Models\Metric;
use App\Models\MetricEntry;
use App\Models\User;
use App\Visibility;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\withoutExceptionHandling;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Exception\HttpException;

pest()->use(RefreshDatabase::class);

afterEach(function (): void {
    CarbonImmutable::setTestNow();
});

test('home page errors when no primary user is defined', function (): void {
    withoutExceptionHandling();

    expect(fn () => get('/'))
        ->toThrow(HttpException::class, 'No primary user defined.');
});

test('home page shows primary user profile for current day', function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-01-06 12:00:00', 'UTC'));

    $user = User::factory()->create([
        'timezone' => 'UTC',
        'primary' => true,
    ]);

    $response = get('/')->assertOk();

    $crawler = new Crawler($response->getContent());

    expect($crawler->filter('[data-day-picker]')->attr('value'))->toBe('2026-01-06');
    expect($crawler->filter('[data-day-link]')->attr('href'))
        ->toBe(route('user.profile.date', [$user, '2026-01-06']));
});

test('prev/next navigation and day fragment endpoints return the expected day', function (): void {
    $user = User::factory()->create([
        'timezone' => 'UTC',
    ]);

    $response = get(route('user.profile.date', [$user, '2026-01-06']))
        ->assertOk();

    $crawler = new Crawler($response->getContent());

    $prevHref = $crawler->filter('[data-day-prev]')->attr('href');
    $nextHref = $crawler->filter('[data-day-next]')->attr('href');

    expect($prevHref)->toBe(route('user.profile.date', [$user, '2026-01-05']));
    expect($nextHref)->toBe(route('user.profile.date', [$user, '2026-01-07']));

    expect($crawler->filter('[data-day-link]')->attr('href'))
        ->toBe(route('user.profile.date', [$user, '2026-01-06']));

    expect($crawler->filter('[data-day-picker]')->attr('value'))
        ->toBe('2026-01-06');

    expect($crawler->filter('[data-day-calendar]')->count())
        ->toBe(1);

    // Simulate clicking prev/next by following their URLs.
    $prevPage = get($prevHref)->assertOk();
    $prevCrawler = new Crawler($prevPage->getContent());
    expect($prevCrawler->filter('[data-day-picker]')->attr('value'))->toBe('2026-01-05');

    $nextPage = get($nextHref)->assertOk();
    $nextCrawler = new Crawler($nextPage->getContent());
    expect($nextCrawler->filter('[data-day-picker]')->attr('value'))->toBe('2026-01-07');

    // Simulate the JS behavior: it fetches `${href}/day` to get the fragment.
    $prevFragment = get($prevHref . '/day')->assertOk();
    $prevFragmentCrawler = new Crawler($prevFragment->getContent());
    expect($prevFragmentCrawler->filter('[data-day-picker]')->attr('value'))->toBe('2026-01-05');

    $pickedDate = '2026-01-03';
    $pickedFragment = get(route('user.profile.day-fragment', [$user, $pickedDate]))->assertOk();
    $pickedFragmentCrawler = new Crawler($pickedFragment->getContent());
    expect($pickedFragmentCrawler->filter('[data-day-picker]')->attr('value'))->toBe($pickedDate);
});

test('owner sees private metrics/habits but guests only see public', function (): void {
    $date = '2026-01-06';

    $owner = User::factory()->create([
        'timezone' => 'UTC',
    ]);

    $guest = User::factory()->create([
        'timezone' => 'UTC',
    ]);

    $publicOptionMetric = Metric::factory()->for($owner)->create([
        'name' => 'Mood',
        'visibility' => Visibility::PUBLIC,
        'options' => '1,2,3',
    ]);

    $privateNumericMetric = Metric::factory()->for($owner)->create([
        'name' => 'Weight',
        'visibility' => Visibility::PRIVATE,
        'options' => null,
        'min' => 0,
        'max' => 500,
    ]);

    $publicHabit = Habit::factory()->for($owner)->create([
        'name' => 'Meditate',
        'visibility' => Visibility::PUBLIC,
    ]);

    $privateHabit = Habit::factory()->for($owner)->create([
        'name' => 'Secret Habit',
        'visibility' => Visibility::PRIVATE,
    ]);

    actingAs($owner);

    post(route('user.day.store', [$owner, $date]), [
        'metrics' => [
            (string) $publicOptionMetric->id => '2',
            (string) $privateNumericMetric->id => '180',
        ],
        'habits' => [
            (string) $publicHabit->id => '1',
            (string) $privateHabit->id => '1',
        ],
    ])->assertRedirect();

    assertDatabaseHas(MetricEntry::class, [
        'user_id' => $owner->id,
        'metric_id' => $publicOptionMetric->id,
        'date' => $date,
    ]);

    assertDatabaseHas(MetricEntry::class, [
        'user_id' => $owner->id,
        'metric_id' => $privateNumericMetric->id,
        'date' => $date,
    ]);

    assertDatabaseHas(HabitEntry::class, [
        'user_id' => $owner->id,
        'habit_id' => $publicHabit->id,
        'date' => $date,
    ]);

    assertDatabaseHas(HabitEntry::class, [
        'user_id' => $owner->id,
        'habit_id' => $privateHabit->id,
        'date' => $date,
    ]);

    $ownerResponse = get(route('user.profile.day-fragment', [$owner, $date]))
        ->assertOk();

    $ownerResponse->assertSeeText('Mood');
    $ownerResponse->assertSeeText('Weight');
    $ownerResponse->assertSeeText('Meditate');
    $ownerResponse->assertSeeText('Secret Habit');

    $ownerCrawler = new Crawler($ownerResponse->getContent());

    expect($ownerCrawler->filter(
        sprintf('input[type="radio"][name="metrics[%d]"][value="2"]', $publicOptionMetric->id),
    )->attr('checked'))->not->toBeNull();

    expect($ownerCrawler->filter(
        sprintf('input[type="number"][name="metrics[%d]"]', $privateNumericMetric->id),
    )->attr('value'))->toBe('180');

    expect($ownerCrawler->filter(
        sprintf('input[type="checkbox"][name="habits[%d]"]', $publicHabit->id),
    )->attr('checked'))->not->toBeNull();

    expect($ownerCrawler->filter(
        sprintf('input[type="checkbox"][name="habits[%d]"]', $privateHabit->id),
    )->attr('checked'))->not->toBeNull();

    actingAs($guest);

    $guestResponse = get(route('user.profile.day-fragment', [$owner, $date]))
        ->assertOk();

    $guestCrawler = new Crawler($guestResponse->getContent());

    $labels = $guestCrawler->filter('.status-pill__label')->each(
        static fn (Crawler $node): string => trim($node->text()),
    );

    $moodLabel = collect($labels)->first(static fn (string $label): bool => str_starts_with($label, 'Mood:'));
    expect($moodLabel)->not->toBeNull();
    expect($moodLabel)->not->toBe('Mood: —');
    expect($moodLabel)->toContain('2');

    $meditateLabel = collect($labels)->first(static fn (string $label): bool => str_starts_with($label, 'Meditate:'));
    expect($meditateLabel)->not->toBeNull();
    expect($meditateLabel)->toBe('Meditate: ✓');

    expect($labels)->not->toContain('Weight');
    expect($labels)->not->toContain('Secret Habit');

    $guestResponse->assertDontSee('data-day-save-form');
});
