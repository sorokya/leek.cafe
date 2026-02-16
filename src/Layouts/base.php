<?php

declare(strict_types=1);

use App\Utils\LayoutHelper;
use App\Utils\SessionHelper;

$user = SessionHelper::getUser();
$flashSuccess = SessionHelper::getFlashSuccess();
$flashError = SessionHelper::getFlashError();
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?= LayoutHelper::getTheme() ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= LayoutHelper::getTitle() ?></title>
    <meta name="description" content="<?= LayoutHelper::getDescription() ?>">
    <?php foreach (LayoutHelper::getStyleSheets() as $stylesheet): ?>
        <link rel="stylesheet" href="<?= LayoutHelper::getStyleSheetUrl($stylesheet) ?>">
    <?php endforeach; ?>
    <link href="data:image/x-icon;base64,AAABAAEAEBAQAAEABAAoAQAAFgAAACgAAAAQAAAAIAAAAAEABAAAAAAAgAAAAAAAAAAAAAAAEAAAAAAAAACwsLAAAAAAAFlZWQD///8A5ubmAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAERERERERERERMxREREERERMzMURERBEREzMxERERERETMzMzMxEREREzMzMzERERERMyIiMREREREDMzMzERERERMyIiMBERERETMzMzEREREREyIiMxERERETMzMzERERERMiIiMREQAAEzMzMxEREAEzMzMxERERERERERERHAHwAAgA8AAAAHAAAABwAAAB8AAIAfAADAHwAAwA8AAOAHAADwBwAA8AMAAPgDAAAAAwAAAAMAAIAHAADADwAA" rel="icon" type="image/x-icon">
</head>

<body>
    <header>
        <h1>
            <a href="/">Leek.cafe</a>
        </h1>
        <nav class="nav-primary">
            <a href="/" <?= LayoutHelper::is_active_route('/') ? 'class="active"' : '' ?>>Home</a>
            <a href="https://github.com/sorokya/leek.cafe" target="_blank" rel="noopener noreferrer">GitHub</a>
            <?php if ($user instanceof \App\Utils\SessionUser): ?>
                <a href="/settings" <?= LayoutHelper::is_active_route('/settings') ? 'class="active"' : '' ?>>Settings</a>
            <?php endif; ?>
        </nav>
        <nav class="nav-secondary">
            <?php if ($user instanceof \App\Utils\SessionUser): ?>
                <form method="POST" action="/logout">
                    <span>Welcome, <?= htmlspecialchars($user->username) ?>! Not you?</span>
                    <button type="submit">Logout</button>
                </form>
            <?php else: ?>
                <a href="/login" <?= LayoutHelper::is_active_route('/login') ? 'class="active"' : '' ?>>Login</a>
            <?php endif; ?>
            <form method="POST" action="/toggle-theme">
                <button type="submit" aria-label="Toggle Theme">
                    <?php if (LayoutHelper::getTheme() === 'light'): ?>
                        <svg width="16" height="16" viewBox="0 0 64 64" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="32" cy="32" r="12" fill="#FDB813" />
                            <g stroke="#FDB813" stroke-width="4" stroke-linecap="round">
                                <line x1="32" y1="4" x2="32" y2="14" />
                                <line x1="32" y1="50" x2="32" y2="60" />
                                <line x1="4" y1="32" x2="14" y2="32" />
                                <line x1="50" y1="32" x2="60" y2="32" />
                                <line x1="12" y1="12" x2="19" y2="19" />
                                <line x1="45" y1="45" x2="52" y2="52" />
                                <line x1="12" y1="52" x2="19" y2="45" />
                                <line x1="45" y1="19" x2="52" y2="12" />
                            </g>
                        </svg>
                    <?php else: ?>
                        <svg width="16" height="16" viewBox="0 0 64 64" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                            <path d="M44 48c-10.493 0-19-8.507-19-19 0-7.19 4.01-13.44 9.92-16.6C22.38 13.33 12 24.2 12 37.5 12 50.48 22.52 61 35.5 61c9.56 0 17.79-5.77 21.39-14.03A18.92 18.92 0 0 1 44 48Z" fill="#B0BEC5" />
                        </svg>
                    <?php endif; ?>
                </button>
            </form>
        </nav>
    </header>
    <main>
        <?php if ($flashSuccess): ?>
            <div class="flash-message flash-success" x-sync id="flash-success">
                <?= htmlspecialchars($flashSuccess) ?>
            </div>
        <?php else: ?>
            <div x-sync id="flash-success" style="display: none;"></div>
        <?php endif; ?>
        <?php if ($flashError): ?>
            <div class="flash-message flash-error" x-sync id="flash-error">
                <?= htmlspecialchars($flashError) ?>
            </div>
        <?php else: ?>
            <div x-sync id="flash-error" style="display: none;"></div>
        <?php endif; ?>
        <?= LayoutHelper::getContent() ?>
    </main>
    <footer>
        <p>&copy; <?= date('Y') ?> Leek.cafe</p>
    </footer>
    <script type="text/javascript">
        window.APP_ENV = <?= json_encode($_ENV['APP_ENV'] ?? 'production') ?>;
        <?php if ($_ENV['APP_ENV'] === 'development'): ?>
            window.ESBUILD_SERVE_HOST = <?= json_encode($_ENV['ESBUILD_SERVE_HOST'] ?? 'localhost') ?>;
            window.ESBUILD_SERVE_PORT = <?= json_encode($_ENV['ESBUILD_SERVE_PORT'] ?? 3751) ?>;
        <?php endif; ?>
    </script>
    <?php foreach (LayoutHelper::getScripts() as $script): ?>
        <script src="/js/<?= htmlspecialchars($script) ?>"></script>
    <?php endforeach; ?>
</body>

</html>