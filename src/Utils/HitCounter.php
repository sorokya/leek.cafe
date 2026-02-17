<?php

declare(strict_types=1);

namespace App\Utils;

use App\Database;

class HitCounter
{
    private const int HIT_LIMIT_SECONDS = 5;

    public static function increment(): int
    {
        $now = time();
        $sessionHits = SessionHelper::getInt('hit_counter', -1);
        $lastSave = SessionHelper::getInt('hit_counter_saved_at', $now - self::HIT_LIMIT_SECONDS - 1);

        if ($sessionHits >= 0 && $now - $lastSave < self::HIT_LIMIT_SECONDS) {
            // Return current hit count without incrementing
            return $sessionHits;
        }

        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $insertStmt = $pdo->prepare("INSERT INTO `meta` (`key`, `value`) VALUES ('hit_counter', 1) ON DUPLICATE KEY UPDATE `value` = `value` + 1");
            $insertStmt->execute();
            $queryStmt = $pdo->prepare("SELECT `value` FROM `meta` WHERE `key` = 'hit_counter'");
            $queryStmt->execute();
            $pdo->commit();
        } catch (\Exception $exception) {
            $pdo->rollBack();
            throw $exception;
        }

        $result = $queryStmt->fetchColumn();
        $hitCount = (int) $result;

        SessionHelper::setInt('hit_counter', $hitCount);
        SessionHelper::setInt('hit_counter_saved_at', $now);

        return $hitCount;
    }
}
