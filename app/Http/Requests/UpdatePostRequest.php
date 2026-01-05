<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class UpdatePostRequest extends ContentRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['embeds']);

        return $rules;
    }
}
