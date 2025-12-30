<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final readonly class UniqueProjectUrl implements ValidationRule
{
    public function __construct(
        private ?string $slug = null,
    ) {}

    /**
     * Run the validation rule.
     *
     * @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = \App\Models\Project::query()->where('url', $value);

        if ($this->slug !== null) {
            $content = \App\Models\Content::where('slug', $this->slug)->first();
            if ($content?->project?->id) {
                $query->where('id', '!=', $content->project->id);
            }
        }

        if ($query->exists()) {
            $fail('The :attribute has already been taken.');
        }
    }
}
