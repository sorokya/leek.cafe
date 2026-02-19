<?php

declare(strict_types=1);

use App\Utils\HitCounter;
use App\Utils\LayoutHelper;
use App\Utils\SessionHelper;

$user = SessionHelper::getUser();
$flashSuccess = SessionHelper::getFlashSuccess();
$flashError = SessionHelper::getFlashError();
$hits = (string) HitCounter::increment();
$hits = str_pad($hits, 9, '0', STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="en">

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
    <nav id="primary-nav" <?= SessionHelper::getBool('primary_nav_collapsed') ? 'class="collapsed"' : '' ?>>
        <div class="nav-background"></div>
        <div class="nav-foreground">
            <?php if (!SessionHelper::getBool('primary_nav_collapsed')): ?>
                <h1>
                    <a href="/">
                        <img src="/img/logo.gif" alt="Leek.cafe" />
                    </a>
                </h1>
                <menu class="nav-menu">
                    <li><a href="/projects" <?= LayoutHelper::is_active_route('/projects') ? 'class="active"' : '' ?>>
                            <img src="/img/gifs/projects.gif" alt="Projects" />
                            projects</a></li>
                    <li><a href="/contact" <?= LayoutHelper::is_active_route('/contact') ? 'class="active"' : '' ?>>
                            <img src="/img/gifs/mailbox.gif" alt="Contact" />
                            contact
                        </a></li>
                    <li class="separator"></li>
                    <li><a href="/anime" <?= LayoutHelper::is_active_route('/anime') ? 'class="active"' : '' ?>>
                            <img src="/img/gifs/anime.gif" alt="Anime" />
                            anime</a></li>
                    <li><a href="/movies" <?= LayoutHelper::is_active_route('/movies') ? 'class="active"' : '' ?>>
                            <img src="/img/gifs/film.gif" alt="Movies" />
                            movies</a></li>
                    <li><a href="/games" <?= LayoutHelper::is_active_route('/games') ? 'class="active"' : '' ?>>
                            <img src="/img/gifs/link.gif" alt="Games" />
                            games
                        </a></li>

                    <li class="separator"></li>
                    <li><a href="/guestbook" <?= LayoutHelper::is_active_route('/guestbook') ? 'class="active"' : '' ?>>
                            <img src="/img/gifs/book.gif" alt="Guestbook" />
                            guestbook</a></li>
                </menu>

                <div class="hit-counter">
                    <span class="hit-counter-label">hits:</span>
                    <div class="hit-counter-value">
                        <?php for ($i = 0; $i < 9; $i++): ?>
                            <span class="hit-digit"><?= $hits[$i] ?></span>
                        <?php endfor; ?>
                    </div>
                </div>

            <?php endif; ?>
        </div>

        <form action="/collapse-nav" method="post" id="collapse-nav-form" x-target="collapse-nav-form primary-nav">
            <button type="submit" id="collapse-nav-button" title="<?= SessionHelper::getBool('primary_nav_collapsed') ? 'Expand navigation' : 'Collapse navigation' ?>">
                <span class="collapse-icon"><?= SessionHelper::getBool('primary_nav_collapsed') ? '&gt;&gt;' : '&lt;&lt;' ?></span>
            </button>
        </form>
    </nav>

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

        <?php if ($music = LayoutHelper::getMusic()): ?>
            <form id="toggle-music-form" action="/toggle-music" method="post" x-target="toggle-music-button" @ajax:after="$dispatch('toggle-music')">
                <button type="submit" aria-label="Toggle music" id="toggle-music-button" <?= SessionHelper::getBool('auto_play_enabled') ? 'class="active"' : '' ?>>
                    <img src="/img/gifs/music.gif" alt="Toggle Music" loading="lazy" />
                </button>
                <?php if ($music['artist'] !== '' && $music['artist'] !== '0'): ?>
                    <span class="music-artist">
                        <?php if ($music['link'] !== '' && $music['link'] !== '0'): ?>
                            <a href="<?= htmlspecialchars($music['link']) ?>" target="_blank" rel="noopener noreferrer">
                                <?= htmlspecialchars($music['artist']) ?>
                            </a>
                        <?php else: ?>
                            <?= htmlspecialchars($music['artist']) ?>
                        <?php endif; ?>
                    </span>
                <?php endif; ?>
            </form>
        <?php endif; ?>

        <?= LayoutHelper::getContent() ?>
    </main>
    <script type="text/javascript">
        window.APP_ENV = <?= json_encode($_ENV['APP_ENV'] ?? 'production') ?>;
        window.AUTO_PLAY_ENABLED = <?= json_encode(SessionHelper::getBool('auto_play_enabled')) ?>;
        <?php if ($_ENV['APP_ENV'] === 'development'): ?>
            window.ESBUILD_SERVE_HOST = <?= json_encode($_ENV['ESBUILD_SERVE_HOST'] ?? 'localhost') ?>;
            window.ESBUILD_SERVE_PORT = <?= json_encode($_ENV['ESBUILD_SERVE_PORT'] ?? 3751) ?>;
        <?php endif; ?>
    </script>
    <?php foreach (LayoutHelper::getScripts() as $script): ?>
        <script src="/js/<?= htmlspecialchars($script) ?>"></script>
    <?php endforeach; ?>
    <?php if ($_ENV['APP_ENV'] !== 'development'): ?>
        <script defer src="https://stats.leek.cafe/script.js" data-website-id="3f8acfa2-41a7-4445-ad03-de0e36ee7af5"></script>
    <?php endif; ?>
</body>

</html>