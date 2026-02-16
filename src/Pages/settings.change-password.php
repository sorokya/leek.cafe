<?php

declare(strict_types=1);

use App\Authentication\ChangePasswordAction;
use App\Authentication\ChangePasswordRequest;
use App\Data\PDO;
use App\Utils\LayoutHelper;
use App\Utils\ResponseHelper;
use App\Utils\SessionHelper;

$user = SessionHelper::getUser();
if (!$user instanceof \App\Utils\SessionUser) {
    ResponseHelper::redirect('/login');
}

LayoutHelper::assertRequestMethod('POST');

$action = new ChangePasswordAction(new PDO(), new ChangePasswordRequest(
    $user->id,
    $_POST['password'] ?? '',
    $_POST['confirm_password'] ?? '',
    $_POST['current_password'] ?? '',
));

if ($action->execute()) {
    SessionHelper::flashSuccess('Password changed successfully.');
} else {
    SessionHelper::flashError($action->error ?? 'Failed to change password.');
}

ResponseHelper::redirect('/settings');
