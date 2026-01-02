<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\get;
use function Pest\Laravel\withoutMiddleware;

pest()->use(RefreshDatabase::class);

test('login persists across requests', function (): void {
    $user = User::factory()->create([
        'username' => fake()->userName(),
        'password' => Hash::make('secret-password'),
    ]);

    withoutMiddleware([VerifyCsrfToken::class, ValidateCsrfToken::class])->post('/login', [
        'username' => $user->username,
        'password' => 'secret-password',
    ])->assertRedirect('/');

    get('/')->assertOk();

    assertAuthenticatedAs($user);
    expect(Auth::id())->toBe($user->getKey());
});
