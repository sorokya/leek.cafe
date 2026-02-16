<?php

declare(strict_types=1);

use App\Authentication\LoginAction;
use App\Authentication\LoginRequest;
use App\Data\PDO;
use App\Models\Session;
use App\Utils\LayoutHelper;
use App\Utils\ResponseHelper;
use App\Utils\SessionHelper;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request = new LoginRequest(
        $_POST['username'] ?? '',
        $_POST['password'] ?? '',
    );

    $loginAction = new LoginAction(new PDO());
    $session = $loginAction->execute($request);
    if ($session instanceof Session) {
        $_SESSION['session_token'] = $session->token;
        ResponseHelper::redirect('/');
    }

    SessionHelper::flashError('Invalid username or password.');
}

LayoutHelper::assertRequestMethod('GET', 'POST');
LayoutHelper::begin('Login');
?>

<form action="/login" method="POST" class="form">
    <div>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit">Login</button>
</form>

<?php LayoutHelper::end();
