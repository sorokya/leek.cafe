<?php

declare(strict_types=1);

use function Pest\Laravel\get;

test('welcome page is accessible', function () {
    get('/')->assertOk();
});

test('posts page is accessible', function () {
    get('/posts')->assertOk();
});

test('projects page is accessible', function () {
    get('/projects')->assertOk();
});

test('thoughts page is accessible', function () {
    get('/thoughts')->assertOk();
});

test('media page is accessible', function () {
    get('/media')->assertOk();
});

test('health endpoint returns ok', function () {
    get('/health')
        ->assertOk()
        ->assertJson(['status' => 'ok']);
});

test('sitemap endpoint is accessible', function () {
    get('/sitemap.xml')->assertOk();
});
