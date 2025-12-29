<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('login');
    }

    public function showSetPassword(Request $request): View|RedirectResponse
    {
        $username = $request->query('username');
        if (!is_string($username) || strlen($username) < 3) {
            abort(400);
        }

        $user = User::findByUsername($username);
        if (!$user || $user->password !== null) {
            abort(403);
        }

        return view('set-password', [
            'username' => $user->username,
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'min:3'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        $username = (string) $validated['username'];
        $password = (string) $validated['password'];
        $remember = (bool) ($validated['remember'] ?? false);

        $user = User::findByUsername($username);
        if (!$user) {
            return $this->fakeHashAndBail();
        }

        if ($user->password === null) {
            return redirect()->route('auth.show-set-password', ['username' => $user->username]);
        }

        if (!Hash::check($password, $user->password)) {
            return back()->withErrors([
                'username' => 'The provided credentials do not match our records.',
            ])->onlyInput('username');
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    public function setPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'min:3'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::findByUsername((string) $validated['username']);
        if (!$user || $user->password !== null) {
            abort(403);
        }

        $user->password = (string) $validated['password'];
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function fakeHashAndBail(): RedirectResponse
    {
        Hash::make('password');

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }
}
