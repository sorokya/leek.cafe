<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Metric;
use App\Models\User;
use App\Visibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class MetricController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $validated = $request->validate([
            'metric_name' => ['required', 'string', 'max:255', Rule::unique('metrics', 'name')->where('user_id', $user->id)],
            'metric_visibility' => ['required', 'integer', Rule::in([Visibility::PRIVATE->value, Visibility::PUBLIC->value])],
            'metric_icon' => ['nullable', 'string', 'max:255', 'regex:/^heroicon-[soc]-[a-z0-9-]+$/'],
            'metric_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'metric_min' => ['nullable', 'numeric'],
            'metric_max' => ['nullable', 'numeric'],
            'metric_options' => ['nullable', 'string', 'max:255'],
        ]);

        $metric = Metric::query()->create([
            'user_id' => $user->id,
            'name' => (string) $validated['metric_name'],
            'visibility' => (int) $validated['metric_visibility'],
            'icon' => is_string($validated['metric_icon'] ?? null) ? $validated['metric_icon'] : null,
            'color' => is_string($validated['metric_color'] ?? null) ? $validated['metric_color'] : null,
            'min' => is_numeric($validated['metric_min'] ?? null) ? (string) $validated['metric_min'] : null,
            'max' => is_numeric($validated['metric_max'] ?? null) ? (string) $validated['metric_max'] : null,
            'options' => is_string($validated['metric_options'] ?? null) ? $validated['metric_options'] : null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $metric->id,
                'name' => $metric->name,
                'visibility' => $metric->visibility->value,
                'icon' => $metric->icon,
                'color' => $metric->color,
                'min' => $metric->min,
                'max' => $metric->max,
                'options' => $metric->options,
            ], 201);
        }

        return to_route('profile.show-settings')->with('status', 'Metric added.');
    }

    public function update(Request $request, Metric $metric): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);
        abort_unless($metric->user_id === $user->id, 403);

        $validated = $request->validate([
            'metric_name_value' => [
                'required',
                'string',
                'max:255',
                Rule::unique('metrics', 'name')
                    ->where('user_id', $user->id)
                    ->ignore($metric->id),
            ],
            'metric_visibility_value' => ['required', 'integer', Rule::in([Visibility::PRIVATE->value, Visibility::PUBLIC->value])],
            'metric_icon_value' => ['nullable', 'string', 'max:255', 'regex:/^heroicon-[soc]-[a-z0-9-]+$/'],
            'metric_color_value' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'metric_min_value' => ['nullable', 'numeric'],
            'metric_max_value' => ['nullable', 'numeric'],
            'metric_options_value' => ['nullable', 'string', 'max:255'],
        ]);

        $metric->update([
            'name' => (string) $validated['metric_name_value'],
            'visibility' => (int) $validated['metric_visibility_value'],
            'icon' => is_string($validated['metric_icon_value'] ?? null) ? $validated['metric_icon_value'] : null,
            'color' => is_string($validated['metric_color_value'] ?? null) ? $validated['metric_color_value'] : null,
            'min' => is_numeric($validated['metric_min_value'] ?? null) ? (string) $validated['metric_min_value'] : null,
            'max' => is_numeric($validated['metric_max_value'] ?? null) ? (string) $validated['metric_max_value'] : null,
            'options' => is_string($validated['metric_options_value'] ?? null) ? $validated['metric_options_value'] : null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $metric->id,
                'name' => $metric->name,
                'visibility' => $metric->visibility->value,
                'icon' => $metric->icon,
                'color' => $metric->color,
                'min' => $metric->min,
                'max' => $metric->max,
                'options' => $metric->options,
            ]);
        }

        return to_route('profile.show-settings')->with('status', 'Metric updated.');
    }

    public function destroy(Request $request, Metric $metric): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);
        abort_unless($metric->user_id === $user->id, 403);

        $metric->delete();

        if ($request->expectsJson()) {
            return response()->json([], 204);
        }

        return to_route('profile.show-settings')->with('status', 'Metric deleted.');
    }
}
