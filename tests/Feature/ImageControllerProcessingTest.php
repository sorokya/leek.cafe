<?php

declare(strict_types=1);

use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

pest()->use(RefreshDatabase::class);

test('/img returns Processing until media exists', function (): void {
    $hash = hash('sha256', 'leek-test-hash');
    Image::create([
        'hash' => $hash,
        'extension' => 'mp4',
    ]);

    $short = substr($hash, 0, 12);

    get('/img/' . $short)
        ->assertOk()
        ->assertSeeText('Processing');

    get('/img/' . $short . '/thumbnail')
        ->assertOk()
        ->assertSeeText('Processing');
});
