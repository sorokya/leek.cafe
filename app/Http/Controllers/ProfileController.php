<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProfileController extends Controller
{
    public function showSettings(): View
    {
        $user = Auth::user();
        if (!$user) {
            throw new \RuntimeException('Authenticated user not found.');
        }

        return view('settings', [
            'name' => $user->name,
        ]);
    }

    public function updateSettings(): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->password) {
            throw new \RuntimeException('Authenticated user not found.');
        }

        $validated = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'new_password' => ['nullable', 'confirmed', Password::default()],
        ]);

        if (!Hash::check($validated['password'], $user->password)) {
            return back()->withErrors([
                'password' => 'The provided password is incorrect.',
            ]);
        }

        $user->name = $validated['name'];

        if (!empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return redirect()->route('profile.show-settings')->with('status', 'Settings updated successfully.');
    }
}
