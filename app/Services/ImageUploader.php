<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use App\Models\Image;

final class ImageUploader
{
    public function upload(UploadedFile $file): int
    {
        $this->optimize($file);
        $filesize = getimagesize($file->getPathname());
        $filename = $this->store($file);

        $image = Image::query()->where('filename', $filename)->first();
        if ($image) {
            return $image->id;
        }

        $image = new Image();
        $image->filename = $filename;
        $image->width = is_array($filesize) ? $filesize[0] : 0;
        $image->height = is_array($filesize) ? $filesize[1] : 0;
        $image->save();

        return $image->id;
    }

    private function optimize(UploadedFile $file): void
    {
        $optimizerChain = OptimizerChainFactory::create();
        $optimizerChain->optimize($file->getPathname());
    }

    private function store(UploadedFile $file): string
    {
        $filename = $this->getFileName($file);
        $firstTwoChars = substr($filename, 0, 2);
        $path = storage_path('app/public/uploads/' . $firstTwoChars . '/');

        if (file_exists($path . $filename)) {
            return $filename;
        }

        $file->move($path, $filename);
        return $filename;
    }

    private function getFileName(UploadedFile $file): string
    {
        return hash_file('sha256', $file->getPathname()) . '.' . $this->getExtension($file);
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
