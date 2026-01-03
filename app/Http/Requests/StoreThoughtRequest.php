<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class StoreThoughtRequest extends ContentRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => 'Post ' . now()->format('YmdHis'),
        ]);
    }
}
