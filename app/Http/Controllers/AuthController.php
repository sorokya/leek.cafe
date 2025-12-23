<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('login');
    }

    public function showSetPassword(Request $request): View|RedirectResponse
    {
        $username = $request->query('username');
        if (!is_string($username) || strlen($username) < 3) {
            return redirect()->route('auth.show-login');
        }

        $user = User::findByUsername($username);
        if (!$user || $user->password !== null) {
            return redirect()->route('auth.show-login');
        }

        return view('set-password');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->only('username', 'password');

        $user = User::findByUsername($credentials['username']);
        if (!$user) {
            return $this->fakeHashAndBail();
        }

        if ($user->password === null) {
            return redirect()->route('auth.show-set-password', ['username' => $user->username]);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'username' => 'The provided credentials do not match our records.',
            ])->onlyInput('username');
        }

        // TODO: Cookie and session stuff

        return redirect('/');
    }

    private function fakeHashAndBail(): RedirectResponse {
        Hash::make('password');
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }
}
