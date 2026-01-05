<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Image;
use App\Support\Ffmpeg;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Image as SpatieImage;
use Spatie\ImageOptimizer\OptimizerChainFactory;

final class ProcessUploadedMedia implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly string $hash) {}

    public function handle(): void
    {
        $image = Image::query()->where('hash', $this->hash)->first();
        if (! $image instanceof Image) {
            return;
        }

        $firstTwoChars = substr($image->hash, 0, 2);

        $originalPath = storage_path('app/private/uploads/originals/' . $firstTwoChars . '/' . $image->hash . '.source');
        if (! file_exists($originalPath)) {
            return;
        }

        $publicDir = storage_path('app/public/uploads/' . $firstTwoChars);
        if (! is_dir($publicDir)) {
            mkdir($publicDir, 0755, true);
        }

        if ($image->extension === 'mp4') {
            $this->processVideo($originalPath, $publicDir, $image->hash);

            return;
        }

        $this->processImage($originalPath, $publicDir, $image->hash, $image->extension);
    }

    private function processImage(string $originalPath, string $publicDir, string $hash, string $extension): void
    {
        $finalPath = $publicDir . '/' . $hash . '.' . $extension;
        $thumbPath = $publicDir . '/' . $hash . '_thumb.' . $extension;

        if (file_exists($finalPath) && file_exists($thumbPath)) {
            @unlink($originalPath);

            return;
        }

        try {
            if (! copy($originalPath, $finalPath)) {
                return;
            }

            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->optimize($finalPath);

            SpatieImage::load($finalPath)
                ->fit(Fit::Contain, 300, 200)
                ->save($thumbPath);

            if (file_exists($finalPath) && file_exists($thumbPath)) {
                @unlink($originalPath);
            }
        } catch (\Throwable) {
            return;
        }
    }

    private function processVideo(string $originalPath, string $publicDir, string $hash): void
    {
        $finalPath = $publicDir . '/' . $hash . '.mp4';
        $thumbPath = $publicDir . '/' . $hash . '_thumb.jpg';

        if (file_exists($finalPath) && file_exists($thumbPath)) {
            @unlink($originalPath);

            return;
        }

        $timeoutConfig = config('media.ffmpeg_timeout_seconds', 300);
        $timeout = is_int($timeoutConfig) ? $timeoutConfig : 300;

        $crfConfig = config('media.h264_crf', 23);
        $crf = is_int($crfConfig) ? $crfConfig : 23;

        $audioBitrateConfig = config('media.audio_bitrate', '128k');
        $audioBitrate = is_string($audioBitrateConfig) ? $audioBitrateConfig : '128k';

        $ffmpeg = resolve(Ffmpeg::class);

        $transcodeOk = $ffmpeg->transcodeToMp4(
            inputPath: $originalPath,
            outputPath: $finalPath,
            timeoutSeconds: $timeout,
            crf: $crf,
            audioBitrate: $audioBitrate,
        );

        if (! $transcodeOk || ! file_exists($finalPath)) {
            return;
        }

        $posterOk = $ffmpeg->generatePoster(
            inputPath: $finalPath,
            outputJpgPath: $thumbPath,
            timeoutSeconds: $timeout,
        );

        if (! $posterOk || ! file_exists($thumbPath)) {
            return;
        }

        @unlink($originalPath);
    }
}
