<?php

declare(strict_types=1);

namespace App\Models;

use App\Database;
use DateTime;

class User
{
    public int $id;

    public string $username;

    public string $displayName;

    public DateTime $createdAt;

    public ?DateTime $updatedAt = null;

    private const string COLUMNS = 'users.id, users.username, IFNULL(users.display_name, users.username) AS display_name, users.created_at, users.updated_at';

    /**
     * @param array{id: int|string, username: string, display_name: string, created_at: string, updated_at: string|null} $data
     */
    private function fill(array $data): void
    {
        $this->id = (int)$data['id'];
        $this->username = $data['username'];
        $this->displayName = $data['display_name'];
        $this->createdAt = new DateTime($data['created_at']);
        $this->updatedAt = $data['updated_at'] ? new DateTime($data['updated_at']) : null;
    }

    public static function findBySessionToken(string $token): ?self
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT ' . self::COLUMNS . ' FROM users JOIN sessions ON users.id = sessions.user_id WHERE sessions.session_token = :session_token AND sessions.expires_at > NOW()');
        $stmt->execute(['session_token' => $token]);

        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }

        $user = new self();
        $user->fill($data);
        return $user;
    }

    public static function findById(int $id): ?self
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT ' . self::COLUMNS . ' FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }

        $user = new self();
        $user->fill($data);
        return $user;
    }
}
