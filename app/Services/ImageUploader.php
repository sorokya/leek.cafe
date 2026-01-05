<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\ProcessUploadedMedia;
use App\Models\Image;
use Illuminate\Http\UploadedFile;

final class ImageUploader
{
    public function upload(UploadedFile $file): Image
    {
        $hash = $this->getHash($file);
        $extension = $this->getExtension($file);

        $firstTwoChars = substr($hash, 0, 2);
        $publicDir = storage_path('app/public/uploads/' . $firstTwoChars);
        $finalPath = $publicDir . '/' . $hash . '.' . $extension;
        $thumbPath = $extension === 'mp4'
            ? $publicDir . '/' . $hash . '_thumb.jpg'
            : $publicDir . '/' . $hash . '_thumb.' . $extension;

        $existing = Image::query()->where('hash', $hash)->first();
        if ($existing instanceof Image) {
            if (file_exists($finalPath) && file_exists($thumbPath)) {
                return $existing;
            }

            $this->stageAndDispatch($file, $hash, $firstTwoChars);

            return $existing;
        }

        $image = Image::create([
            'hash' => $hash,
            'extension' => $extension,
        ]);

        $this->stageAndDispatch($file, $hash, $firstTwoChars);

        return $image;
    }

    private function stageAndDispatch(UploadedFile $file, string $hash, string $firstTwoChars): void
    {
        $stagingDir = storage_path('app/private/uploads/originals/' . $firstTwoChars);
        if (! is_dir($stagingDir)) {
            mkdir($stagingDir, 0755, true);
        }

        $stagingPath = $stagingDir . '/' . $hash . '.source';
        if (! file_exists($stagingPath)) {
            $file->move($stagingDir, $hash . '.source');
        }

        dispatch(new ProcessUploadedMedia($hash));
    }

    private function getHash(UploadedFile $file): string
    {
        $algorithmConfig = config('media.hash_algorithm', 'xxh3');
        $algorithm = is_string($algorithmConfig) && $algorithmConfig !== '' ? $algorithmConfig : 'xxh3';

        if (! in_array($algorithm, hash_algos(), true)) {
            $fallbackConfig = config('media.hash_fallback_algorithm', 'sha256');
            $fallback = is_string($fallbackConfig) && $fallbackConfig !== '' ? $fallbackConfig : 'sha256';
            $algorithm = in_array($fallback, hash_algos(), true) ? $fallback : 'sha256';
        }

        $hash = hash_file($algorithm, $file->getPathname());
        throw_unless($hash, \RuntimeException::class, 'Failed to compute file hash.');

        return $hash;
    }

    private function getExtension(UploadedFile $file): string
    {
        return match ($file->getClientMimeType()) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'video/mp4', 'video/quicktime', 'video/webm', 'video/x-matroska' => 'mp4',
            default => throw new \InvalidArgumentException('Unsupported image type: ' . $file->getClientMimeType()),
        };
    }
}
