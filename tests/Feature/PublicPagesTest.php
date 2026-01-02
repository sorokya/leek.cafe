<?php

declare(strict_types=1);

use function Pest\Laravel\get;

test('welcome page is accessible', function (): void {
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
