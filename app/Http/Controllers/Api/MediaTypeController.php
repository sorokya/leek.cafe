<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MediaType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class MediaTypeController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'max:255', 'unique:media_types,type'],
        ]);

        $mediaType = MediaType::query()->create([
            'type' => (string) $validated['type'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $mediaType->id,
                'type' => $mediaType->type,
            ], 201);
        }

        return redirect()
            ->route('profile.show-settings')
            ->with('status', 'Media type added.');
    }

    public function update(Request $request, MediaType $mediaType): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'type_value' => [
                'required',
                'string',
                'max:255',
                Rule::unique('media_types', 'type')->ignore($mediaType->id),
            ],
        ]);

        $mediaType->type = (string) $validated['type_value'];
        $mediaType->save();

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $mediaType->id,
                'type' => $mediaType->type,
            ]);
        }

        return redirect()
            ->route('profile.show-settings')
            ->with('status', 'Media type updated.');
    }

    public function destroy(Request $request, MediaType $mediaType): JsonResponse|RedirectResponse
    {
        $mediaType->delete();

        if ($request->expectsJson()) {
            return response()->json([], 204);
        }

        return redirect()
            ->route('profile.show-settings')
            ->with('status', 'Media type deleted.');
    }
}
