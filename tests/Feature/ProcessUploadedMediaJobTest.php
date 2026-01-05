<?php

declare(strict_types=1);

use App\Jobs\ProcessUploadedMedia;
use App\Models\Image;
use App\Support\Ffmpeg;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->use(RefreshDatabase::class);

test('ProcessUploadedMedia deletes original after successful video processing', function (): void {
    $hash = hash('sha256', 'leek-video-success');
    $firstTwo = substr($hash, 0, 2);

    $image = Image::create([
        'hash' => $hash,
        'extension' => 'mp4',
    ]);

    $originalDir = storage_path('app/private/uploads/originals/' . $firstTwo);
    if (! is_dir($originalDir)) {
        mkdir($originalDir, 0755, true);
    }

    $originalPath = $originalDir . '/' . $hash . '.source';
    file_put_contents($originalPath, 'not-a-real-video');

    app()->bind(Ffmpeg::class, fn (): \App\Support\Ffmpeg => new class extends Ffmpeg
    {
        public function transcodeToMp4(string $inputPath, string $outputPath, int $timeoutSeconds, int $crf, string $audioBitrate): bool
        {
            $dir = dirname($outputPath);
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($outputPath, 'fake-mp4');

            return true;
        }

        public function generatePoster(string $inputPath, string $outputJpgPath, int $timeoutSeconds): bool
        {
            $dir = dirname($outputJpgPath);
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($outputJpgPath, 'fake-jpg');

            return true;
        }
    });

    (new ProcessUploadedMedia($image->hash))->handle();

    $publicDir = storage_path('app/public/uploads/' . $firstTwo);
    expect(file_exists($publicDir . '/' . $hash . '.mp4'))->toBeTrue();
    expect(file_exists($publicDir . '/' . $hash . '_thumb.jpg'))->toBeTrue();
    expect(file_exists($originalPath))->toBeFalse();
});

test('ProcessUploadedMedia keeps original on video processing failure', function (): void {
    $hash = hash('sha256', 'leek-video-failure');
    $firstTwo = substr($hash, 0, 2);

    $image = Image::create([
        'hash' => $hash,
        'extension' => 'mp4',
    ]);

    $originalDir = storage_path('app/private/uploads/originals/' . $firstTwo);
    if (! is_dir($originalDir)) {
        mkdir($originalDir, 0755, true);
    }

    $originalPath = $originalDir . '/' . $hash . '.source';
    file_put_contents($originalPath, 'not-a-real-video');

    app()->bind(Ffmpeg::class, fn (): \App\Support\Ffmpeg => new class extends Ffmpeg
    {
        public function transcodeToMp4(string $inputPath, string $outputPath, int $timeoutSeconds, int $crf, string $audioBitrate): bool
        {
            return false;
        }
    });

    (new ProcessUploadedMedia($image->hash))->handle();

    $publicDir = storage_path('app/public/uploads/' . $firstTwo);
    expect(file_exists($publicDir . '/' . $hash . '.mp4'))->toBeFalse();
    expect(file_exists($originalPath))->toBeTrue();
});
