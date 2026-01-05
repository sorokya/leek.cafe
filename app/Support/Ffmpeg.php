<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class Ffmpeg
{
    public function probeDurationSeconds(string $path, int $timeoutSeconds): ?float
    {
        $process = new Process([
            'ffprobe',
            '-v',
            'error',
            '-show_entries',
            'format=duration',
            '-of',
            'default=noprint_wrappers=1:nokey=1',
            $path,
        ]);

        $process->setTimeout($timeoutSeconds);
        $process->run();

        if (! $process->isSuccessful()) {
            return null;
        }

        $output = trim($process->getOutput());
        if ($output === '') {
            return null;
        }

        $duration = (float) $output;
        if ($duration <= 0) {
            return null;
        }

        return $duration;
    }

    /** @return array<int, string> */
    public function transcodeToMp4Args(string $inputPath, string $outputPath, int $crf, string $audioBitrate): array
    {
        return [
            'ffmpeg',
            '-y',
            '-i',
            $inputPath,
            '-map_metadata',
            '-1',
            '-map_chapters',
            '-1',
            '-sn',
            '-dn',
            '-vf',
            "scale='min(1920,iw)':-2",
            '-c:v',
            'libx264',
            '-crf',
            (string) $crf,
            '-preset',
            'medium',
            '-pix_fmt',
            'yuv420p',
            '-movflags',
            '+faststart',
            '-c:a',
            'aac',
            '-b:a',
            $audioBitrate,
            $outputPath,
        ];
    }

    public function transcodeToMp4(
        string $inputPath,
        string $outputPath,
        int $timeoutSeconds,
        int $crf,
        string $audioBitrate,
    ): bool {
        $process = new Process($this->transcodeToMp4Args($inputPath, $outputPath, $crf, $audioBitrate));
        $process->setTimeout($timeoutSeconds);
        $process->run();

        Log::debug('FFmpeg transcode output: ' . $process->getOutput());

        if (! $process->isSuccessful()) {
            Log::error('FFmpeg transcode failed: ' . $process->getErrorOutput());
        }

        return $process->isSuccessful();
    }

    public function generatePoster(
        string $inputPath,
        string $outputJpgPath,
        int $timeoutSeconds,
    ): bool {
        $process = new Process([
            'ffmpeg',
            '-y',
            '-ss',
            '1',
            '-i',
            $inputPath,
            '-vframes',
            '1',
            '-vf',
            'scale=300:200:force_original_aspect_ratio=decrease',
            $outputJpgPath,
        ]);

        $process->setTimeout($timeoutSeconds);
        $process->run();

        Log::debug('FFmpeg poster generation output: ' . $process->getOutput());

        if (! $process->isSuccessful()) {
            Log::error('FFmpeg poster generation failed: ' . $process->getErrorOutput());
        }

        return $process->isSuccessful();
    }
}
