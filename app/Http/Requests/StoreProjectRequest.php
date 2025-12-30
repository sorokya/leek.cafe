<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

final class StoreProjectRequest extends ContentRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'url' => ['required', 'string', 'max:2048', 'url', Rule::unique('projects', 'url')],
        ]);
    }
}
