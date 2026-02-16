<?php

declare(strict_types=1);

namespace App\Authentication;

readonly class LoginRequest
{
    public string $username;

    public function __construct(string $username, public string $password)
    {
        $this->username = trim($username);
    }

    public function validate(): bool
    {
        if (strlen($this->username) < 3) {
            return false;
        }

        return strlen($this->password) >= 6;
    }
}
