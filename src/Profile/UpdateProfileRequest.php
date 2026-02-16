<?php

declare(strict_types=1);

namespace App\Profile;

class UpdateProfileRequest
{
    public function __construct(
        public int $userId,
        public string $displayName,
    ) {
    }

    public function validate(): ?string
    {
        if ($this->displayName !== '' && (strlen($this->displayName) < 3 || strlen($this->displayName) > 50)) {
            return 'Display name must be between 3 and 50 characters.';
        }

        return null;
    }
}
