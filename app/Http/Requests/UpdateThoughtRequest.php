<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Content;

final class UpdateThoughtRequest extends ContentRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->has('title')) {
            $slug = $this->route('slug');

            if (is_string($slug)) {
                $existing = Content::query()->where('slug', $slug)->first();

                if ($existing instanceof Content) {
                    $this->merge(['title' => $existing->title]);
                }
            }
        }
    }
}
