<?php

namespace App\Http\Controllers;

use App\Models\MediaStatus;
use App\Models\MediaType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProfileController extends Controller
{
    public function showSettings(): View | RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        if (!$user->password) {
            return redirect()->route('auth.show-set-password', [
                'username' => $user->username,
            ]);
        }

        return view('settings', [
            'name' => $user->name,
            'timezone' => $user->timezone,
            'mediaStatuses' => MediaStatus::query()->get(),
            'mediaTypes' => MediaType::query()->get(),
        ]);
    }

    public function updateSettings(): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->password) {
            abort(403);
        }

        $validated = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'new_password' => ['nullable', 'confirmed', Password::default()],
            'timezone' => ['required', 'string', 'max:255'],
        ]);

        if (!Hash::check($validated['password'], $user->password)) {
            return back()->withErrors([
                'password' => 'The provided password is incorrect.',
            ]);
        }

        $user->name = $validated['name'];
        $user->timezone = $validated['timezone'];

        if (!empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return redirect()->route('profile.show-settings')->with('status', 'Settings updated successfully.');
    }
}
