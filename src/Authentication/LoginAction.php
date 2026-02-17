<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Database;
use App\Models\Session;

class LoginAction
{
    public function execute(LoginRequest $request): ?Session
    {
        if (!$request->validate()) {
            return null;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->execute(['username' => $request->username]);

        $user = $stmt->fetch();
        if (!$user || !password_verify($request->password, (string) $user['password_hash'])) {
            return null;
        }

        return Session::create($user['id']);
    }
}
