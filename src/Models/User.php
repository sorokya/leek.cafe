<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\PDO;
use DateTime;

class User
{
    public int $id;

    public string $username;

    public string $displayName;

    public DateTime $createdAt;

    public ?DateTime $updatedAt = null;

    public static function findBySessionToken(PDO $pdo, string $token): ?self
    {
        $stmt = $pdo->prepare('SELECT u.id, u.username, u.display_name, u.created_at, u.updated_at FROM users u JOIN sessions s ON u.id = s.user_id WHERE s.session_token = :session_token AND s.expires_at > NOW()');
        $stmt->execute(['session_token' => $token]);

        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }

        $user = new self();
        $user->id = (int)$data['id'];
        $user->username = $data['username'];
        $user->displayName = $data['display_name'];
        $user->createdAt = new DateTime($data['created_at']);
        $user->updatedAt = $data['updated_at'] ? new DateTime($data['updated_at']) : null;
        return $user;
    }
}
