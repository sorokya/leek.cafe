<?php

declare(strict_types=1);

use App\Jobs\ProcessUploadedMedia;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->use(RefreshDatabase::class);

test('processing job deletes original after successful image processing', function (): void {
    if (! function_exists('imagecreatetruecolor')) {
        $this->markTestSkipped('GD extension required.');
    }

    $tmp = tempnam(sys_get_temp_dir(), 'leek-img-');
    expect($tmp)->toBeString();

    $im = imagecreatetruecolor(8, 8);
    imagefilledrectangle($im, 0, 0, 7, 7, imagecolorallocate($im, 255, 0, 0));
    imagepng($im, $tmp);

    $hash = hash_file('sha256', $tmp);
    expect($hash)->toBeString();

    Image::create([
        'hash' => $hash,
        'extension' => 'png',
    ]);

    $firstTwo = substr($hash, 0, 2);
    $stagingDir = storage_path('app/private/uploads/originals/' . $firstTwo);
    if (! is_dir($stagingDir)) {
        mkdir($stagingDir, 0755, true);
    }

    $stagingPath = $stagingDir . '/' . $hash . '.source';
    copy($tmp, $stagingPath);

    (new ProcessUploadedMedia($hash))->handle();

    expect(file_exists($stagingPath))->toBeFalse();

    $publicDir = storage_path('app/public/uploads/' . $firstTwo);
    expect(file_exists($publicDir . '/' . $hash . '.png'))->toBeTrue();
    expect(file_exists($publicDir . '/' . $hash . '_thumb.png'))->toBeTrue();
});

test('processing job keeps original when image processing fails', function (): void {
    $hash = str_repeat('b', 64);

    Image::create([
        'hash' => $hash,
        'extension' => 'png',
    ]);

    $firstTwo = substr($hash, 0, 2);
    $stagingDir = storage_path('app/private/uploads/originals/' . $firstTwo);
    if (! is_dir($stagingDir)) {
        mkdir($stagingDir, 0755, true);
    }

    $stagingPath = $stagingDir . '/' . $hash . '.source';
    file_put_contents($stagingPath, 'not-a-real-image');

    (new ProcessUploadedMedia($hash))->handle();

    expect(file_exists($stagingPath))->toBeTrue();
});
