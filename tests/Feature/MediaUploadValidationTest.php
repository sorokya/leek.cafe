<?php

declare(strict_types=1);

use App\Models\User;
use App\Support\Ffmpeg;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\withoutMiddleware;

use Symfony\Component\Process\Process;

pest()->use(RefreshDatabase::class);

beforeEach(function (): void {
    withoutMiddleware([VerifyCsrfToken::class, ValidateCsrfToken::class]);
});

test('upload-images rejects disallowed mimetype', function (): void {
    $user = User::factory()->create();

    $file = UploadedFile::fake()->create('file.txt', 10, 'text/plain');

    actingAs($user)
        ->post('/thoughts/upload-images', [
            'image' => [$file],
        ], [
            'Accept' => 'application/json',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['image.0']);
});

test('upload-images rejects too-long video by duration', function (): void {
    $probe = new Process(['ffmpeg', '-version']);
    $probe->run();
    if (! $probe->isSuccessful()) {
        $this->markTestSkipped('ffmpeg not available to generate a test mp4 fixture.');
    }

    $user = User::factory()->create();

    $outPath = tempnam(sys_get_temp_dir(), 'leek-video-');
    if ($outPath === false) {
        $this->markTestSkipped('Unable to create temp file.');
    }

    $outPath .= '.mp4';

    $makeVideo = new Process([
        'ffmpeg',
        '-y',
        '-f',
        'lavfi',
        '-i',
        'color=c=black:s=16x16:d=2',
        '-f',
        'lavfi',
        '-i',
        'anullsrc=channel_layout=stereo:sample_rate=44100',
        '-shortest',
        '-c:v',
        'libx264',
        '-pix_fmt',
        'yuv420p',
        '-c:a',
        'aac',
        '-b:a',
        '64k',
        $outPath,
    ]);
    $makeVideo->setTimeout(30);
    $makeVideo->run();

    if (! $makeVideo->isSuccessful() || ! file_exists($outPath)) {
        $this->markTestSkipped('Failed to generate mp4 fixture via ffmpeg.');
    }

    app()->bind(Ffmpeg::class, fn (): \App\Support\Ffmpeg => new class extends Ffmpeg
    {
        public function probeDurationSeconds(string $path, int $timeoutSeconds): float
        {
            return 9999.0;
        }
    });

    $file = new UploadedFile($outPath, 'video.mp4', 'video/mp4', null, true);

    actingAs($user)
        ->post('/thoughts/upload-images', [
            'image' => [$file],
        ], [
            'Accept' => 'application/json',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['image.0']);
});
