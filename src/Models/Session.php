<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\PDO;
use DateTime;

class Session
{
    public string $token;

    public int $userId;

    public ?string $userAgent = null;

    public ?string $ipAddress = null;

    public DateTime $createdAt;

    public DateTime $expiresAt;

    public function expired(): bool
    {
        return $this->expiresAt < new DateTime();
    }

    public function invalidate(PDO $pdo): void
    {
        $stmt = $pdo->prepare('UPDATE sessions SET expires_at = NOW() WHERE session_token = :session_token');
        $stmt->execute(['session_token' => $this->token]);
    }

    public static function create(PDO $pdo, int $userId): self
    {
        $session = new self();
        $session->userId = $userId;
        $session->token = bin2hex(random_bytes(32));
        $session->createdAt = new DateTime();
        $session->expiresAt = new DateTime()->modify('+7 days');
        $session->userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $session->ipAddress = self::getUserIpAddress();

        $stmt = $pdo->prepare('INSERT INTO sessions (user_id, session_token, created_at, expires_at, user_agent, ip_address) VALUES (:user_id, :session_token, :created_at, :expires_at, :user_agent, :ip_address)');
        $stmt->execute([
            'user_id' => $session->userId,
            'session_token' => $session->token,
            'created_at' => $session->createdAt->format('Y-m-d H:i:s'),
            'expires_at' => $session->expiresAt->format('Y-m-d H:i:s'),
            'user_agent' => $session->userAgent,
            'ip_address' => $session->ipAddress,
        ]);

        return $session;
    }

    public static function findByToken(PDO $pdo, string $token): ?self
    {
        $stmt = $pdo->prepare('SELECT * FROM sessions WHERE session_token = :session_token AND expires_at > NOW()');
        $stmt->execute(['session_token' => $token]);

        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }

        $session = new self();
        $session->userId = (int)$data['user_id'];
        $session->token = $data['session_token'];
        $session->createdAt = new DateTime($data['created_at']);
        $session->expiresAt = new DateTime($data['expires_at']);
        $session->userAgent = $data['user_agent'];
        $session->ipAddress = $data['ip_address'];

        return $session;
    }

    private static function getUserIpAddress(): ?string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $forwardedIps = explode(',', (string) $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($forwardedIps[0]);
        }

        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }

        return null;
    }
}
