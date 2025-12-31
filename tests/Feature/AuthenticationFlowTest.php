<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('user can complete full authentication flow', function () {
    // Visit login page
    get('/login')->assertOk();

    // Create a user (simulating registration)
    $user = User::factory()->create([
        'username' => 'testuser',
        'password' => 'password123',
    ]);

    // Login
    post('/login', [
        'username' => 'testuser',
        'password' => 'password123',
    ])->assertRedirect('/');

    // Verify authenticated
    actingAs($user)
        ->get('/')
        ->assertOk();

    // Logout
    post('/logout')->assertRedirect('/');
})->uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('user can navigate through protected pages after login', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get('/posts/new')
        ->assertOk();

    actingAs($user)
        ->get('/projects/new')
        ->assertOk();

    actingAs($user)
        ->get('/settings')
        ->assertOk();
})->uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('unauthenticated user is redirected from protected pages', function () {
    get('/posts/new')->assertRedirect('/login');
    get('/projects/new')->assertRedirect('/login');
    get('/settings')->assertRedirect('/login');
});
