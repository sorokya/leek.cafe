<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MediaStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Str;

final class MediaStatusController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'max:255', 'unique:media_statuses,status'],
            'icon' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $mediaStatus = MediaStatus::query()->create([
            'status' => (string) $validated['status'],
            'slug' => Str::slug($validated['status']),
            'icon' => isset($validated['icon']) ? (string) $validated['icon'] : null,
            'color' => isset($validated['color']) ? (string) $validated['color'] : null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $mediaStatus->id,
                'slug' => $mediaStatus->slug,
                'status' => $mediaStatus->status,
                'icon' => $mediaStatus->icon,
                'color' => $mediaStatus->color,
            ], 201);
        }

        return redirect()
            ->route('profile.show-settings')
            ->with('status', 'Media status added.');
    }

    public function update(Request $request, MediaStatus $mediaStatus): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'status_value' => [
                'required',
                'string',
                'max:255',
                Rule::unique('media_statuses', 'status')->ignore($mediaStatus->id),
            ],
            'icon_value' => ['nullable', 'string', 'max:255'],
            'color_value' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $mediaStatus->status = (string) $validated['status_value'];
        $mediaStatus->icon = isset($validated['icon_value']) ? (string) $validated['icon_value'] : null;
        $mediaStatus->color = isset($validated['color_value']) ? (string) $validated['color_value'] : null;
        $mediaStatus->save();

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $mediaStatus->id,
                'status' => $mediaStatus->status,
                'icon' => $mediaStatus->icon,
                'color' => $mediaStatus->color,
            ]);
        }

        return redirect()
            ->route('profile.show-settings')
            ->with('status', 'Media status updated.');
    }

    public function destroy(Request $request, MediaStatus $mediaStatus): JsonResponse|RedirectResponse
    {
        $mediaStatus->delete();

        if ($request->expectsJson()) {
            return response()->json([], 204);
        }

        return redirect()
            ->route('profile.show-settings')
            ->with('status', 'Media status deleted.');
    }
}
