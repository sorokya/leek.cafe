<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Response;

final class ImageController extends Controller
{
    public function serve(string $hash): Response
    {
        $image = Image::query()
            ->where('hash', 'like', $hash . '%')
            ->first();

        abort_unless($image instanceof Image, 404);

        $firstTwoChars = substr($image->hash, 0, 2);
        $path = storage_path('app/public/uploads/' . $firstTwoChars . '/' . $image->hash . '.' . $image->extension);

        if (! file_exists($path)) {
            return new Response('Processing', 200, [
                'Content-Type' => 'text/plain; charset=UTF-8',
                'Cache-Control' => 'no-store',
            ]);
        }

        $mimeType = mime_content_type($path) ?: 'application/octet-stream';
        $content = file_get_contents($path);

        $etag = sha1($image->hash . '|' . filemtime($path) . '|' . filesize($path));

        return new Response($content, 200, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'ETag' => '"' . $etag . '"',
        ]);
    }

    public function serveThumbnail(string $hash): Response
    {
        $image = Image::query()
            ->where('hash', 'like', $hash . '%')
            ->first();

        abort_unless($image instanceof Image, 404);

        $firstTwoChars = substr($image->hash, 0, 2);
        $thumbName = $image->extension === 'mp4'
            ? $image->hash . '_thumb.jpg'
            : $image->hash . '_thumb.' . $image->extension;
        $path = storage_path('app/public/uploads/' . $firstTwoChars . '/' . $thumbName);

        if (! file_exists($path)) {
            return new Response('Processing', 200, [
                'Content-Type' => 'text/plain; charset=UTF-8',
                'Cache-Control' => 'no-store',
            ]);
        }

        $mimeType = mime_content_type($path) ?: 'application/octet-stream';
        $content = file_get_contents($path);

        $etag = sha1($image->hash . '|thumb|' . filemtime($path) . '|' . filesize($path));

        return new Response($content, 200, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'ETag' => '"' . $etag . '"',
        ]);
    }
}
