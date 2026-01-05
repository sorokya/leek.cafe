<?php

declare(strict_types=1);

use App\Jobs\ProcessUploadedMedia;
use App\Models\Image;
use App\Models\User;
use App\Support\Ffmpeg;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\withoutMiddleware;

pest()->use(RefreshDatabase::class);

beforeEach(function (): void {
    withoutMiddleware([VerifyCsrfToken::class, ValidateCsrfToken::class]);
});

test('upload rejects disallowed mimetype', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);

    $file = UploadedFile::fake()->create('evil.txt', 10, 'text/plain');

    actingAs($user)
        ->postJson('/posts/upload-images', [
            'image' => [$file],
        ])
        ->assertStatus(422);
});

test('upload rejects videos that exceed max duration', function (): void {
    config()->set('media.max_video_duration_seconds', 3);

    $ffmpeg = \Mockery::mock(Ffmpeg::class);
    $ffmpeg->shouldReceive('probeDurationSeconds')->andReturn(999.0);
    app()->instance(Ffmpeg::class, $ffmpeg);

    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);

    $file = UploadedFile::fake()->create('too-long.mp4', 10, 'video/mp4');

    actingAs($user)
        ->postJson('/posts/upload-images', [
            'image' => [$file],
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['image.0']);
});

test('upload returns hashes and dispatches processing job', function (): void {
    $ffmpeg = \Mockery::mock(Ffmpeg::class);
    $ffmpeg->shouldReceive('probeDurationSeconds')->andReturn(1.0);
    app()->instance(Ffmpeg::class, $ffmpeg);

    Bus::fake();

    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);

    $file = UploadedFile::fake()->create('ok.jpg', 10, 'image/jpeg');

    $response = actingAs($user)
        ->postJson('/posts/upload-images', [
            'image' => [$file],
        ])
        ->assertOk()
        ->assertJsonStructure(['hashes']);

    $hash = $response->json('hashes.0');
    expect($hash)->toBeString();

    Bus::assertDispatched(ProcessUploadedMedia::class);
});

test('img routes return Processing until outputs exist', function (): void {
    $hash = str_repeat('a', 64);

    Image::create([
        'hash' => $hash,
        'extension' => 'mp4',
    ]);

    get('/img/' . substr($hash, 0, 12))
        ->assertOk()
        ->assertSeeText('Processing');

    get('/img/' . substr($hash, 0, 12) . '/thumbnail')
        ->assertOk()
        ->assertSeeText('Processing');
});
