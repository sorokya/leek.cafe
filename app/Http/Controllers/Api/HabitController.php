<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Habit;
use App\Models\User;
use App\Visibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class HabitController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $validated = $request->validate([
            'habit_name' => ['required', 'string', 'max:255', Rule::unique('habits', 'name')->where('user_id', $user->id)],
            'habit_visibility' => ['required', 'integer', Rule::in([Visibility::PRIVATE->value, Visibility::PUBLIC->value])],
            'habit_icon' => ['nullable', 'string', 'max:255', 'regex:/^heroicon-[soc]-[a-z0-9-]+$/'],
            'habit_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $habit = Habit::query()->create([
            'user_id' => $user->id,
            'name' => (string) $validated['habit_name'],
            'visibility' => (int) $validated['habit_visibility'],
            'icon' => is_string($validated['habit_icon'] ?? null) ? $validated['habit_icon'] : null,
            'color' => is_string($validated['habit_color'] ?? null) ? $validated['habit_color'] : null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $habit->id,
                'name' => $habit->name,
                'visibility' => $habit->visibility->value,
                'icon' => $habit->icon,
                'color' => $habit->color,
            ], 201);
        }

        return to_route('profile.show-settings')->with('status', 'Habit added.');
    }

    public function update(Request $request, Habit $habit): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);
        abort_unless($habit->user_id === $user->id, 403);

        $validated = $request->validate([
            'habit_name_value' => [
                'required',
                'string',
                'max:255',
                Rule::unique('habits', 'name')
                    ->where('user_id', $user->id)
                    ->ignore($habit->id),
            ],
            'habit_visibility_value' => ['required', 'integer', Rule::in([Visibility::PRIVATE->value, Visibility::PUBLIC->value])],
            'habit_icon_value' => ['nullable', 'string', 'max:255', 'regex:/^heroicon-[soc]-[a-z0-9-]+$/'],
            'habit_color_value' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $habit->update([
            'name' => (string) $validated['habit_name_value'],
            'visibility' => (int) $validated['habit_visibility_value'],
            'icon' => is_string($validated['habit_icon_value'] ?? null) ? $validated['habit_icon_value'] : null,
            'color' => is_string($validated['habit_color_value'] ?? null) ? $validated['habit_color_value'] : null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $habit->id,
                'name' => $habit->name,
                'visibility' => $habit->visibility->value,
                'icon' => $habit->icon,
                'color' => $habit->color,
            ]);
        }

        return to_route('profile.show-settings')->with('status', 'Habit updated.');
    }

    public function destroy(Request $request, Habit $habit): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);
        abort_unless($habit->user_id === $user->id, 403);

        $habit->delete();

        if ($request->expectsJson()) {
            return response()->json([], 204);
        }

        return to_route('profile.show-settings')->with('status', 'Habit deleted.');
    }
}
