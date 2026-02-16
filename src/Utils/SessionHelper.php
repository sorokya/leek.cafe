<?php

declare(strict_types=1);

namespace App\Utils;

use App\Data\PDO;
use App\Models\User;

class SessionUser
{
    public function __construct(public int $id, public string $username, public string $displayName)
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

        return new SessionUser((int) $currentUser['id'], (string) $currentUser['username'], (string) $currentUser['display_name']);
    }

    public static function setUser(User $user): void
    {
        $_SESSION['current_user'] = [
            'id' => $user->id,
            'username' => $user->username,
            'display_name' => $user->displayName,
        ];
    }

    public static function refreshUser(PDO $pdo): void
    {
        $user = self::getUser();
        if (!$user instanceof \App\Utils\SessionUser) {
            return;
        }

        $userId = $user->id;

        $freshUser = User::findById($pdo, $userId);
        if ($freshUser instanceof \App\Models\User) {
            self::setUser($freshUser);
        }
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
