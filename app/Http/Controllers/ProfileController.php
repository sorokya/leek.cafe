<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\Habit;
use App\Models\MediaStatus;
use App\Models\MediaType;
use App\Models\Metric;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class ProfileController extends Controller
{
    public function showSettings(): View|RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        if (! $user->password) {
            return to_route('auth.show-set-password', [
                'username' => $user->username,
            ]);
        }

        return view('settings', [
            'name' => $user->name,
            'timezone' => $user->timezone,
            'mediaStatuses' => MediaStatus::query()->get(),
            'mediaTypes' => MediaType::query()->get(),
            'metrics' => Metric::query()->where('user_id', $user->id)->orderBy('name')->get(),
            'habits' => Habit::query()->where('user_id', $user->id)->orderBy('name')->get(),
        ]);
    }

    public function updateSettings(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();
        abort_if(! $user || ! $user->password, 403);

        $validated = $request->validated();

        if (! is_string($validated['password']) || ! Hash::check($validated['password'], $user->password)) {
            return back()->withErrors([
                'password' => 'The provided password is incorrect.',
            ]);
        }

        $data = $request->only(['name', 'timezone']);

        if (is_string($validated['new_password'])) {
            $data['password'] = Hash::make($validated['new_password']);
        }

        $user->update($data);

        return to_route('profile.show-settings')->with('status', 'Settings updated successfully.');
    }
}
