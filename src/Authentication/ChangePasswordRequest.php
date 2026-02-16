<?php

declare(strict_types=1);

namespace App\Authentication;

readonly class ChangePasswordRequest
{
    public function __construct(public int $userId, public string $newPassword, public string $confirmNewPassword, public string $currentPassword)
    {
    }

    public function validate(): ?string
    {
        if ($this->userId <= 0) {
            return 'Invalid user ID.';
        }

        if (strlen($this->newPassword) < 6) {
            return 'New password must be at least 6 characters long.';
        }

        if ($this->newPassword !== $this->confirmNewPassword) {
            return 'New password and confirmation do not match.';
        }

        if ($this->currentPassword === '') {
            return 'Current password is required.';
        }

        return null;
    }
}
