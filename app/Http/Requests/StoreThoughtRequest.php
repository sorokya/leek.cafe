<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Support\Str;

final class StoreThoughtRequest extends ContentRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->has('title')) {
            $this->merge([
                'title' => sprintf(
                    'Thought %s %s',
                    now()->format('Y-m-d H:i'),
                    Str::lower(Str::random(6)),
                ),
            ]);
        }
    }
}
