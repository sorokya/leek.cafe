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

LayoutHelper::assertRequestMethod('GET', 'POST');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
}

LayoutHelper::begin('Settings', 'Manage your account settings.');
LayoutHelper::addStyleSheet('settings.css');
?>

<div class="settings-forms">
    <form action="/settings/change-password" method="POST" id="change-password-form" x-target="change-password-form" class="form">
        <h2>Change Password</h2>
        <div>
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" value="">
        </div>
        <div>
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" value="">
        </div>
        <div>
            <label for="current_password">Current Password (required to save changes):</label>
            <input type="password" id="current_password" name="current_password" value="" required>
        </div>
        <button type="submit">Change Password</button>
    </form>

    <form action="/settings/update-profile" method="POST" id="update-profile-form" x-target="update-profile-form" class="form">
        <h2>Update Profile</h2>
        <div>
            <label for="display_name">Display Name:</label>
            <input type="text" id="display_name" name="display_name" value="<?= $user->displayName === $user->username ? '' : htmlspecialchars($user->displayName) ?>">
        </div>
        <button type="submit">Update Profile</button>
    </form>
</div>

<?php LayoutHelper::end();
