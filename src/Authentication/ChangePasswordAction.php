<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Database;

class ChangePasswordAction
{
    public ?string $error = null;

    public function __construct(private readonly ChangePasswordRequest $request)
    {
    }

    public function execute(): bool
    {
        if ($error = $this->request->validate()) {
            $this->error = $error;
            return false;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = :user_id');
        $stmt->execute(['user_id' => $this->request->userId]);

        $user = $stmt->fetch();

        if (!$user || !password_verify($this->request->currentPassword, (string) $user['password_hash'])) {
            $this->error = 'Current password is incorrect.';
            return false;
        }

        $newPasswordHash = password_hash($this->request->newPassword, PASSWORD_ARGON2ID);

        $updateStmt = $pdo->prepare('UPDATE users SET password_hash = :password_hash, updated_at = NOW() WHERE id = :user_id');
        return $updateStmt->execute([
            'password_hash' => $newPasswordHash,
            'user_id' => $this->request->userId,
        ]);
    }
}
