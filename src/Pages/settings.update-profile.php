<?php

declare(strict_types=1);

use App\Data\PDO;
use App\Profile\UpdateProfileAction;
use App\Profile\UpdateProfileRequest;
use App\Utils\LayoutHelper;
use App\Utils\ResponseHelper;
use App\Utils\SessionHelper;

$user = SessionHelper::getUser();
if (!$user instanceof \App\Utils\SessionUser) {
    ResponseHelper::redirect('/login');
}

LayoutHelper::assertRequestMethod('POST');

$pdo = new PDO();
$action = new UpdateProfileAction($pdo, new UpdateProfileRequest(
    $user->id,
    $_POST['display_name'] ?? '',
), $user);

if ($action->execute()) {
    SessionHelper::refreshUser($pdo);
    SessionHelper::flashSuccess('Profile updated successfully.');
} else {
    SessionHelper::flashError($action->error ?? 'Failed to update profile.');
}

ResponseHelper::redirect('/settings');
