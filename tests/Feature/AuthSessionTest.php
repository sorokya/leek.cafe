<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\post;

test('login persists across requests', function () {
    $user = User::factory()->create([
        'username' => 'alice',
        'password' => Hash::make('secret-password'),
    ]);

    post('/login', [
        'username' => 'alice',
        'password' => 'secret-password',
    ])->assertRedirect('/');

    $this->get('/')->assertOk();

    assertAuthenticatedAs($user);
    expect(Auth::id())->toBe($user->getKey());
})->uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
