<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use App\Models\Image;

final class ImageUploader
{
    public function upload(UploadedFile $file): string
    {
        $this->optimize($file);
        $hash = $this->store($file);

        $image = Image::query()->where('hash', $hash)->first();
        if ($image) {
            return substr($image->hash, 0, 12);
        }

        $image = new Image();
        $image->hash = $hash;
        $image->extension = $this->getExtension($file);
        $image->save();

        return substr($image->hash, 0, 12);
    }

    private function optimize(UploadedFile $file): void
    {
        $optimizerChain = OptimizerChainFactory::create();
        $optimizerChain->optimize($file->getPathname());
    }

    private function store(UploadedFile $file): string
    {
        $hash = $this->getHash($file);
        $extension = $this->getExtension($file);
        $firstTwoChars = substr($hash, 0, 2);
        $path = storage_path('app/public/uploads/' . $firstTwoChars . '/');

        if (file_exists($path . $hash . '.' . $extension)) {
            return $hash;
        }

        $file->move($path, $hash . '.' . $extension);
        return $hash;
    }

    private function getHash(UploadedFile $file): string
    {
        $hash = hash_file('sha256', $file->getPathname());
        if (!$hash) {
            throw new \RuntimeException('Failed to compute file hash.');
        }
        return $hash;
    }

    private function getExtension(UploadedFile $file): string
    {
        return match ($file->getClientMimeType()) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            default => throw new \InvalidArgumentException('Unsupported image type: ' . $file->getClientMimeType()),
        };
    }
}
