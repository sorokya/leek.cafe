<?php

declare(strict_types=1);

use App\ContentType;
use App\Models\Content;
use App\Models\User;
use App\Visibility;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\get;

pest()->use(RefreshDatabase::class);

test('thought published time renders in the content created timezone (show and index)', function (): void {
    $timezone = 'Europe/Stockholm';

    $user = User::factory()->create([
        'timezone' => $timezone,
    ]);

    $content = Content::factory()
        ->for($user)
        ->create([
            'content_type' => ContentType::THOUGHT->value,
            'visibility' => Visibility::PUBLIC->value,
            'created_timezone' => $timezone,
            'body' => 'Hello world',
        ]);

    $content->thought()->create();

    // 11:22 in Stockholm (UTC+1 in January) is 10:22 UTC.
    $createdAtUtc = CarbonImmutable::parse('2026-01-06 10:22:00', 'UTC');

    DB::table('contents')
        ->where('id', $content->id)
        ->update([
            'created_at' => $createdAtUtc->toDateTimeString(),
            'updated_at' => $createdAtUtc->toDateTimeString(),
        ]);

    $expectedLocal = $createdAtUtc->setTimezone($timezone);

    get(route('thoughts.show', ['slug' => $content->slug]))
        ->assertOk()
        ->assertSee($expectedLocal->format('M j, Y g:i A'))
        ->assertSee($expectedLocal->toW3cString());

    get(route('thoughts.index'))
        ->assertOk()
        ->assertSee($expectedLocal->format('M j, Y g:i A'))
        ->assertSee($expectedLocal->toW3cString());
});

test('post published date renders in the content created timezone (index and show)', function (): void {
    $timezone = 'Europe/Stockholm';

    $user = User::factory()->create([
        'timezone' => $timezone,
    ]);

    $content = Content::factory()
        ->for($user)
        ->create([
            'content_type' => ContentType::POST->value,
            'visibility' => Visibility::PUBLIC->value,
            'created_timezone' => $timezone,
            'title' => 'Timezone boundary post',
            'body' => '# Hello',
        ]);

    $content->post()->create();

    // 00:30 on Jan 6 in Stockholm is 23:30 UTC on Jan 5.
    $createdAtUtc = CarbonImmutable::parse('2026-01-05 23:30:00', 'UTC');

    DB::table('contents')
        ->where('id', $content->id)
        ->update([
            'created_at' => $createdAtUtc->toDateTimeString(),
            'updated_at' => $createdAtUtc->toDateTimeString(),
        ]);

    $expectedLocal = $createdAtUtc->setTimezone($timezone);

    get(route('posts.index'))
        ->assertOk()
        ->assertSee($expectedLocal->format('F j, Y'))
        ->assertSee($expectedLocal->toW3cString());

    get(route('posts.show', ['slug' => $content->slug]))
        ->assertOk()
        ->assertSee($expectedLocal->format('F j, Y'))
        ->assertSee($expectedLocal->toW3cString());
});
