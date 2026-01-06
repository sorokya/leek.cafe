<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

pest()->use(RefreshDatabase::class);

test('welcome page is accessible', function (): void {
    User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
        'primary' => true,
    ]);
    get('/')->assertOk();
});

test('posts page is accessible', function (): void {
    get('/posts')->assertOk();
});

test('projects page is accessible', function (): void {
    get('/projects')->assertOk();
});

test('thoughts page is accessible', function (): void {
    get('/thoughts')->assertOk();
});

test('media page is accessible', function (): void {
    get('/media')->assertOk();
});

test('health endpoint returns ok', function (): void {
    get('/health')
        ->assertOk()
        ->assertJson(['status' => 'ok']);
});

test('sitemap endpoint is accessible', function (): void {
    get('/sitemap.xml')->assertOk();
});
