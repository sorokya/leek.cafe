<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Support\Str;

final class UpdateThoughtRequest extends ContentRequest
{
    protected function prepareForValidation(): void
    {
        $content = $this->input('content');

        if (is_string($content) && ! $this->has('body')) {
            $this->merge(['body' => $content]);
        }

        if ($this->hasFile('attachment') && ! $this->hasFile('cover')) {
            $this->merge(['cover' => $this->file('attachment')]);
        }

        $body = $this->input('body');

        if (! $this->has('title')) {
            $title = null;

            if (is_string($body)) {
                $normalized = trim(preg_replace('/\s+/', ' ', $body) ?? '');
                $title = Str::limit($normalized, 80, '');
            }

            $fallback = sprintf(
                'Thought %s-%s',
                now()->format('YmdHis'),
                Str::lower(Str::random(6)),
            );

            $this->merge([
                'title' => $title !== null && $title !== ''
                    ? sprintf('%s (%s)', $title, Str::lower(Str::random(4)))
                    : $fallback,
            ]);
        }
    }
}
