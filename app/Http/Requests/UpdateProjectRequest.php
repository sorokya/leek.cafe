<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class UpdateProjectRequest extends ContentRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'url' => [
                'required',
                'string',
                'max:2048',
                'url',
            ],
        ]);
    }
}
