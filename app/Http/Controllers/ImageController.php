<?php

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

        abort_unless($image, 404);

        $firstTwoChars = substr($image->hash, 0, 2);
        $path = storage_path('app/public/uploads/' . $firstTwoChars . '/' . $image->hash . '.' . $image->extension);

        abort_unless(file_exists($path), 404);

        $mimeType = mime_content_type($path) ?: 'application/octet-stream';
        $content = file_get_contents($path);
        return new Response($content, 200, [
            'Content-Type' => $mimeType,
        ]);
    }

    public function serveThumbnail(string $hash): Response
    {
        $image = Image::query()
            ->where('hash', 'like', $hash . '%')
            ->first();

        abort_unless($image, 404);

        $firstTwoChars = substr($image->hash, 0, 2);
        $path = storage_path('app/public/uploads/' . $firstTwoChars . '/' . $image->hash . '_thumb.' . $image->extension);

        abort_unless(file_exists($path), 404);

        $mimeType = mime_content_type($path) ?: 'application/octet-stream';
        $content = file_get_contents($path);
        return new Response($content, 200, [
            'Content-Type' => $mimeType,
        ]);
    }
}
