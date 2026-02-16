<?php

declare(strict_types=1);

use App\Utils\LayoutHelper;
use App\Utils\ResponseHelper;

LayoutHelper::assertRequestMethod('POST');

$currentTheme = LayoutHelper::getTheme();
$newTheme = $currentTheme === 'light' ? 'dark' : 'light';
setcookie('theme', $newTheme, [
    'expires' => time() + 60 * 60 * 24 * 365, // 1 year
    'path' => '/',
    'secure' => $_SERVER['HTTPS'] ?? false,
    'httponly' => true,
    'samesite' => 'Lax',
]);

ResponseHelper::back();
