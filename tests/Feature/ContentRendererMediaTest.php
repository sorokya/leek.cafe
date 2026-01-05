<?php

declare(strict_types=1);

use App\Models\Image;
use App\Services\ContentRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->use(RefreshDatabase::class);

test('ContentRenderer renders mp4 images as <video>', function (): void {
    $hash = hash('sha256', 'leek-render-video');
    Image::create([
        'hash' => $hash,
        'extension' => 'mp4',
    ]);

    $short = substr($hash, 0, 12);

    $renderer = new ContentRenderer;
    $result = $renderer->render('![](@img:' . $short . ')');

    expect((string) $result)
        ->toContain('<video')
        ->toContain('/img/' . $short);
});

test('ContentRenderer renders non-mp4 images as <img>', function (): void {
    $hash = hash('sha256', 'leek-render-image');
    Image::create([
        'hash' => $hash,
        'extension' => 'jpg',
    ]);

    $short = substr($hash, 0, 12);

    $renderer = new ContentRenderer;
    $result = $renderer->render('![](@img:' . $short . ')');

    expect((string) $result)
        ->toContain('<img')
        ->toContain('/img/' . $short);
});
