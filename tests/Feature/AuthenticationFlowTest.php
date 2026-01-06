<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\withoutMiddleware;

pest()->use(RefreshDatabase::class);

beforeEach(function (): void {
    withoutMiddleware([VerifyCsrfToken::class, ValidateCsrfToken::class]);
});

test('user can complete full authentication flow', function (): void {
    // Visit login page
    get('/login')->assertOk();

    // Create a user (simulating registration)
    $user = User::factory()->create([
        'username' => fake()->userName(),
        'password' => Hash::make('password123'),
        'primary' => true,
    ]);

    // Login
    post('/login', [
        'username' => $user->username,
        'password' => 'password123',
    ])->assertRedirect('/');

    // Verify authenticated
    actingAs($user)
        ->get('/')
        ->assertOk();

    // Logout
    post('/logout')->assertRedirect('/');
});

test('user can navigate through protected pages after login', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);

    actingAs($user)
        ->get('/posts/new')
        ->assertOk();

    actingAs($user)
        ->get('/projects/new')
        ->assertOk();

    actingAs($user)
        ->get('/settings')
        ->assertOk();
});

test('unauthenticated user is redirected from protected pages', function (): void {
    get('/posts/new')->assertRedirect('/login');
    get('/projects/new')->assertRedirect('/login');
    get('/settings')->assertRedirect('/login');
});

test('new user is prompted to set password', function (): void {
    $user = User::factory()->create();

    post('/login', [
        'username' => $user->username,
        'password' => 'password',
    ])->assertRedirect('/set-password?username=' . $user->username);

    // Simulate setting password
    post('/set-password?username=' . $user->username, [
        'password' => 'new-secure-password',
        'password_confirmation' => 'new-secure-password',
    ])->assertRedirect('/');

    get('/settings')->assertOk();
});
