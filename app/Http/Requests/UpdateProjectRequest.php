<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $projectId = $this->route('slug') 
            ? \App\Models\Content::where('slug', $this->route('slug'))->first()?->project?->id
            : null;

        return [
            'title' => ['required', 'string', 'max:255'],
            'url' => [
                'required',
                'string',
                'max:2048',
                'url',
                Rule::unique('projects', 'url')->ignore($projectId),
            ],
            'body' => ['required', 'string'],
            'visibility' => ['required', 'integer'],
            'cover' => ['nullable', 'image'],
        ];
    }
}
