<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Data\PDO;
use App\Models\Session;

class LoginAction
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function execute(LoginRequest $request): ?Session
    {
        if (!$request->validate()) {
            return null;
        }

        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->execute(['username' => $request->username]);

        $user = $stmt->fetch();
        if (!$user || !password_verify($request->password, (string) $user['password_hash'])) {
            return null;
        }

        return Session::create($this->pdo, $user['id']);
    }
}
