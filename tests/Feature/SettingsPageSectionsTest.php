<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Models\MediaStatus;
use App\Models\MediaType;
use App\Models\Metric;
use App\Models\User;
use App\Visibility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

use Symfony\Component\DomCrawler\Crawler;

pest()->use(RefreshDatabase::class);

test('/settings loads stored data for all sections', function (): void {
    $user = User::factory()->create([
        'name' => 'Alice Example',
        'timezone' => 'UTC',
        'password' => Hash::make('current-password'),
    ]);

    $mediaType = MediaType::query()->create([
        'type' => 'Book',
        'slug' => 'book',
    ]);

    $mediaStatus = MediaStatus::query()->create([
        'status' => 'In Progress',
        'slug' => 'in-progress',
        'icon' => 'heroicon-o-play',
        'color' => '#00FF00',
    ]);

    $metric = Metric::query()->create([
        'user_id' => $user->id,
        'name' => 'Mood',
        'visibility' => Visibility::PUBLIC,
        'icon' => 'heroicon-o-face-smile',
        'color' => '#112233',
        'min' => null,
        'max' => null,
        'options' => '1,2,3',
    ]);

    $habit = Habit::query()->create([
        'user_id' => $user->id,
        'name' => 'Meditate',
        'visibility' => Visibility::PRIVATE,
        'icon' => 'heroicon-o-heart',
        'color' => '#445566',
    ]);

    actingAs($user);

    $response = get(route('profile.show-settings'))
        ->assertOk();

    $crawler = new Crawler($response->getContent());

    // User settings
    expect($crawler->filter('input#name')->attr('value'))->toBe('Alice Example');
    expect($crawler->filter('select#timezone option[selected]')->attr('value'))->toBe('UTC');

    // Media Types (existing)
    expect($crawler->filter(sprintf('form[action="%s"] input[name="type_value"][value="%s"]',
        route('media-types.update', $mediaType),
        $mediaType->type,
    ))->count())->toBe(1);

    // Media Statuses (existing)
    $mediaStatusForm = $crawler->filter(sprintf('form[action="%s"]', route('media-statuses.update', $mediaStatus)));
    expect($mediaStatusForm->filter('input[name="status_value"]')->attr('value'))->toBe('In Progress');
    expect($mediaStatusForm->filter('input[name="icon_value"]')->attr('value'))->toBe('heroicon-o-play');
    expect($mediaStatusForm->filter('input[name="color_value"]')->attr('value'))->toBe('#00FF00');

    // Metrics (existing)
    $metricForm = $crawler->filter(sprintf('form[action="%s"]', route('metrics.update', $metric)));
    expect($metricForm->filter('input[name="metric_name_value"]')->attr('value'))->toBe('Mood');
    expect($metricForm->filter('select[name="metric_visibility_value"] option[selected]')->attr('value'))
        ->toBe((string) Visibility::PUBLIC->value);
    expect($metricForm->filter('input[name="metric_icon_value"]')->attr('value'))->toBe('heroicon-o-face-smile');
    expect($metricForm->filter('input[name="metric_color_value"]')->attr('value'))->toBe('#112233');
    expect($metricForm->filter('input[name="metric_options_value"]')->attr('value'))->toBe('1,2,3');

    // Habits (existing)
    $habitForm = $crawler->filter(sprintf('form[action="%s"]', route('habits.update', $habit)));
    expect($habitForm->filter('input[name="habit_name_value"]')->attr('value'))->toBe('Meditate');
    expect($habitForm->filter('select[name="habit_visibility_value"] option[selected]')->attr('value'))
        ->toBe((string) Visibility::PRIVATE->value);
    expect($habitForm->filter('input[name="habit_icon_value"]')->attr('value'))->toBe('heroicon-o-heart');
    expect($habitForm->filter('input[name="habit_color_value"]')->attr('value'))->toBe('#445566');
});

test('user settings form persists changes', function (): void {
    $user = User::factory()->create([
        'name' => 'Alice Example',
        'timezone' => 'UTC',
        'password' => Hash::make('current-password'),
    ]);

    actingAs($user);

    post(route('profile.update-settings'), [
        'name' => 'Alice Updated',
        'timezone' => 'America/Los_Angeles',
        'password' => 'current-password',
        'new_password' => null,
        'new_password_confirmation' => null,
    ])->assertRedirect(route('profile.show-settings'));

    assertDatabaseHas(User::class, [
        'id' => $user->id,
        'name' => 'Alice Updated',
        'timezone' => 'America/Los_Angeles',
    ]);
});

test('media types section persists add/update/delete', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('pw'),
    ]);

    actingAs($user);

    post(route('media-types.store'), [
        'type' => 'Movie',
    ])->assertRedirect(route('profile.show-settings'));

    $created = MediaType::query()->where('type', 'Movie')->firstOrFail();

    put(route('media-types.update', $created), [
        'type_value' => 'Film',
    ])->assertRedirect(route('profile.show-settings'));

    assertDatabaseHas(MediaType::class, [
        'id' => $created->id,
        'type' => 'Film',
    ]);

    delete(route('media-types.destroy', $created))
        ->assertRedirect(route('profile.show-settings'));

    assertDatabaseMissing(MediaType::class, [
        'id' => $created->id,
    ]);
});

test('media statuses section persists add/update/delete', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('pw'),
    ]);

    actingAs($user);

    post(route('media-statuses.store'), [
        'status' => 'Planned',
        'icon' => 'heroicon-o-clock',
        'color' => '#AABBCC',
    ])->assertRedirect(route('profile.show-settings'));

    $created = MediaStatus::query()->where('status', 'Planned')->firstOrFail();

    put(route('media-statuses.update', $created), [
        'status_value' => 'Planning',
        'icon_value' => 'heroicon-o-clock',
        'color_value' => '#DDEEFF',
    ])->assertRedirect(route('profile.show-settings'));

    assertDatabaseHas(MediaStatus::class, [
        'id' => $created->id,
        'status' => 'Planning',
        'icon' => 'heroicon-o-clock',
        'color' => '#DDEEFF',
    ]);

    delete(route('media-statuses.destroy', $created))
        ->assertRedirect(route('profile.show-settings'));

    assertDatabaseMissing(MediaStatus::class, [
        'id' => $created->id,
    ]);
});

test('metrics section persists add/update/delete', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('pw'),
    ]);

    actingAs($user);

    post(route('metrics.store'), [
        'metric_name' => 'Focus',
        'metric_visibility' => (string) Visibility::PRIVATE->value,
        'metric_icon' => 'heroicon-o-bolt',
        'metric_color' => '#101010',
        'metric_min' => '0',
        'metric_max' => '10',
        'metric_options' => null,
    ])->assertRedirect(route('profile.show-settings'));

    $created = Metric::query()->where('user_id', $user->id)->where('name', 'Focus')->firstOrFail();

    put(route('metrics.update', $created), [
        'metric_name_value' => 'Deep Focus',
        'metric_visibility_value' => (string) Visibility::PUBLIC->value,
        'metric_icon_value' => 'heroicon-o-bolt',
        'metric_color_value' => '#202020',
        'metric_min_value' => '1',
        'metric_max_value' => '9',
        'metric_options_value' => '1,2,3',
    ])->assertRedirect(route('profile.show-settings'));

    assertDatabaseHas(Metric::class, [
        'id' => $created->id,
        'user_id' => $user->id,
        'name' => 'Deep Focus',
        'visibility' => Visibility::PUBLIC->value,
        'icon' => 'heroicon-o-bolt',
        'color' => '#202020',
        'options' => '1,2,3',
    ]);

    delete(route('metrics.destroy', $created))
        ->assertRedirect(route('profile.show-settings'));

    assertDatabaseMissing(Metric::class, [
        'id' => $created->id,
    ]);
});

test('habits section persists add/update/delete', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('pw'),
    ]);

    actingAs($user);

    post(route('habits.store'), [
        'habit_name' => 'Walk',
        'habit_visibility' => (string) Visibility::PUBLIC->value,
        'habit_icon' => 'heroicon-o-arrow-right',
        'habit_color' => '#123456',
    ])->assertRedirect(route('profile.show-settings'));

    $created = Habit::query()->where('user_id', $user->id)->where('name', 'Walk')->firstOrFail();

    put(route('habits.update', $created), [
        'habit_name_value' => 'Walk Outside',
        'habit_visibility_value' => (string) Visibility::PRIVATE->value,
        'habit_icon_value' => 'heroicon-o-arrow-right',
        'habit_color_value' => '#654321',
    ])->assertRedirect(route('profile.show-settings'));

    assertDatabaseHas(Habit::class, [
        'id' => $created->id,
        'user_id' => $user->id,
        'name' => 'Walk Outside',
        'visibility' => Visibility::PRIVATE->value,
        'icon' => 'heroicon-o-arrow-right',
        'color' => '#654321',
    ]);

    delete(route('habits.destroy', $created))
        ->assertRedirect(route('profile.show-settings'));

    assertDatabaseMissing(Habit::class, [
        'id' => $created->id,
    ]);
});
