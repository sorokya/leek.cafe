<?php

declare(strict_types=1);

namespace App\Utils;

class SessionUser
{
    public function __construct(public int $id, public string $username)
    {
    }
}

class SessionHelper
{
    public static function getUser(): ?SessionUser
    {
        $currentUser = $_SESSION['current_user'] ?? null;
        if (!$currentUser) {
            return null;
        }

        return new SessionUser((int) $currentUser['id'], (string) $currentUser['username']);
    }

    public static function flashSuccess(string $message): void
    {
        $_SESSION['flash_success'] = $message;
    }

    public static function flashError(string $message): void
    {
        $_SESSION['flash_error'] = $message;
    }

    public static function getFlashSuccess(): ?string
    {
        $message = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_success']);
        return $message;
    }

    public static function getFlashError(): ?string
    {
        $message = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);
        return $message;
    }
}
