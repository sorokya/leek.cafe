<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_persists_across_requests(): void
    {
        $user = User::factory()->create([
            'username' => 'alice',
            'password' => 'secret-password',
        ]);

        $this->post('/login', [
            'username' => 'alice',
            'password' => 'secret-password',
        ])->assertRedirect('/');

        $this->get('/')->assertOk();

        $this->assertAuthenticatedAs($user);
        $this->assertSame($user->getKey(), Auth::id());
    }
}
