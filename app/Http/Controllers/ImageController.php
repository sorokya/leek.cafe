<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Image;
use App\Services\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

final class ImageController extends Controller
{
    public function __construct(
        private readonly ImageUploader $imageUploader,
    ) {}

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

    /**
     * Handle image uploads.
     *
     * @return array<string, mixed>
     */
    public function upload(Request $request): array
    {
        $maxUploadKilobytes = Config::integer('media.max_upload_kilobytes', 51200);

        $validator = Validator::make($request->all(), [
            'image' => ['required', 'array'],
            'image.*' => [
                'required',
                'file',
                'mimetypes:image/jpeg,image/png,image/gif,video/mp4,video/quicktime,video/webm,video/x-matroska',
                'max:' . $maxUploadKilobytes,
            ],
        ]);

        $validator->after(function ($validator) use ($request): void {
            $files = $request->file('image', []);
            if (! is_array($files)) {
                return;
            }

            $maxDuration = Config::integer('media.max_video_duration_seconds', 120);
            $timeout = Config::integer('media.ffprobe_timeout_seconds', 10);
            $allowedVideoMimes = [
                'video/mp4',
                'video/quicktime',
                'video/webm',
                'video/x-matroska',
            ];

            foreach ($files as $index => $file) {
                $mime = (string) $file->getClientMimeType();
                if (! in_array($mime, $allowedVideoMimes, true)) {
                    continue;
                }

                $duration = resolve(\App\Support\Ffmpeg::class)
                    ->probeDurationSeconds($file->getPathname(), $timeout);

                if ($duration === null) {
                    $validator->errors()->add('image.' . $index, 'Unable to read video duration.');

                    continue;
                }

                if ($duration > $maxDuration) {
                    $validator->errors()->add(
                        'image.' . $index,
                        'Video is too long (max ' . $maxDuration . 's).',
                    );
                }
            }
        });

        $validated = $validator->validate();

        $images = $validated['image'] ?? [];
        $hashes = [];

        foreach ($images as $image) {
            $img = $this->imageUploader->upload($image);
            $hashes[] = $img->getShortHash();
        }

        return ['hashes' => $hashes];
    }
}
