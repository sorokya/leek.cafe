<?php

declare(strict_types=1);

use App\Utils\LayoutHelper;

LayoutHelper::assertRequestMethod('GET');
LayoutHelper::begin('Games', 'Explore my favorite games on leek.cafe!');
LayoutHelper::addStyleSheet('games.css');

$n64Games = [
    [
        'title' => 'Zelda: Ocarina of Time',
        'year' => 1998,
        'cover' => '/img/games/oot-cover.png',
        'rating' => 5.0,
    ],
    [
        'title' => "Zelda: Majora's Mask",
        'year' => 2000,
        'cover' => '/img/games/mm-cover.png',
        'rating' => 5.0,
    ],
    [
        'title' => 'Super Mario 64',
        'year' => 1996,
        'cover' => '/img/games/sm64-cover.png',
        'rating' => 5.0,
    ],
    [
        'title' => 'Super Smash Bros.',
        'year' => 1999,
        'cover' => '/img/games/smash64-cover.png',
        'rating' => 4.5,
    ],
];

$ps2Games = [
    [
        'title' => 'Jak and Daxter',
        'year' => 2001,
        'cover' => '/img/games/jak1-cover.png',
        'rating' => 5.0,
    ],
    [
        'title' => 'Jak II',
        'year' => 2003,
        'cover' => '/img/games/jak2-cover.png',
        'rating' => 5.0,
    ],
    [
        'title' => 'Jak 3',
        'year' => 2004,
        'cover' => '/img/games/jak3-cover.png',
        'rating' => 5.0,
    ],
    [
        'title' => 'Kingdom Hearts',
        'year' => 2002,
        'cover' => '/img/games/kh1-cover.png',
        'rating' => 5.0,
    ],
    [
        'title' => 'Kingdom Hearts II',
        'year' => 2006,
        'cover' => '/img/games/kh2-cover.png',
        'rating' => 5.0,
    ],
    [
        'title' => 'SSX 3',
        'year' => 2003,
        'cover' => '/img/games/ssx3-cover.png',
        'rating' => 4.5,
    ],
    [
        'title' => 'Bully',
        'year' => 2006,
        'cover' => '/img/games/bully-cover.png',
        'rating' => 4.5,
    ],
    [
        'title' => 'GTA: 3',
        'year' => 2001,
        'cover' => '/img/games/gta3-cover.png',
        'rating' => 4.5,
    ],
    [
        'title' => 'GTA: Vice City',
        'year' => 2002,
        'cover' => '/img/games/gta-vc-cover.png',
        'rating' => 4.5,
    ],
    [
        'title' => 'GTA: San Andreas',
        'year' => 2004,
        'cover' => '/img/games/gta-sa-cover.png',
        'rating' => 5.0,
    ],
];

$snesGames = [
    [
        'title' => 'Super Mario World',
        'year' => 1990,
        'cover' => '/img/games/smw-cover.png',
        'rating' => 5.0,
    ],
    [
        'title' => 'Super Mario: All-Stars',
        'year' => 1993,
        'cover' => '/img/games/smas-cover.png',
        'rating' => 5.0,
    ],
    [
        'title' => 'Donkey Kong Country',
        'year' => 1994,
        'cover' => '/img/games/dkc-cover.png',
        'rating' => 5.0,
    ],
    [
        'title' => 'Mickey Mania',
        'year' => 1994,
        'cover' => '/img/games/mickey-mania-cover.png',
        'rating' => 4.5,
    ],
];

$gameCubeGames = [
    [
        'title' => 'Zelda: The Wind Waker',
        'year' => 2003,
        'cover' => '/img/games/ww-cover.png',
        'rating' => 5.0,
    ],
    [
        'title' => 'Zelda: Twilight Princess',
        'year' => 2006,
        'cover' => '/img/games/tp-cover.png',
        'rating' => 5.0,
    ],
    [
        'title' => 'Super Mario Sunshine',
        'year' => 2002,
        'cover' => '/img/games/sms-cover.png',
        'rating' => 5.0,
    ],
    [
        'title' => "Luigi's Mansion",
        'year' => 2001,
        'cover' => '/img/games/luigi-mansion-cover.png',
        'rating' => 5.0,
    ],
];

$dreamcastGames = [
    [
        'title' => 'Sonic Adventure',
        'year' => 1998,
        'cover' => '/img/games/sonic-adventure-cover.png',
        'rating' => 4.5,
    ],
    [
        'title' => 'Sonic Adventure 2',
        'year' => 2001,
        'cover' => '/img/games/sonic-adventure-2-cover.png',
        'rating' => 4.5,
    ],
];
?>

<?php LayoutHelper::addMusic('/music/astral-observatory.mp3', 'audio/mpeg'); ?>

<h1>Video games are awesome</h1>

<p>
    They're probably my <em>favorite</em> form of entertainment. Exploring and interacting with virtual worlds that
    were painstakingly crafted is just the best!
</p>

<p>
    My favorite games are listed below by console/platform. You can click some of them to see more details.
</p>

<h1 class="img-header">
    <img src="/img/gifs/n64.gif" alt="N64" />
    Nintendo 64
    <img src="/img/gifs/n64.gif" alt="N64" />
</h1>

<ul class="game-list">
    <?php foreach ($n64Games as $game): ?>
        <li>
            <img src="<?= htmlspecialchars($game['cover']) ?>" alt="<?= htmlspecialchars($game['title']) ?>" />
            <h2><?= htmlspecialchars($game['title']) ?> (<?= htmlspecialchars((string) $game['year']) ?>)</h2>
            <span class="rating" title="<?= htmlspecialchars((string) $game['rating']) ?> out of 5" data-rating="<?= htmlspecialchars((string)$game['rating']) ?>"></span>
        </li>
    <?php endforeach; ?>
</ul>

<h1 class="img-header">
    <img src="/img/gifs/dreamcast.gif" alt="Dreamcast" />
    Dreamcast
    <img src="/img/gifs/dreamcast.gif" alt="Dreamcast" />
</h1>

<ul class="game-list">
    <?php foreach ($dreamcastGames as $game): ?>
        <li>
            <img src="<?= htmlspecialchars($game['cover']) ?>" alt="<?= htmlspecialchars($game['title']) ?>" class="disk" />
            <h2><?= htmlspecialchars($game['title']) ?> (<?= htmlspecialchars((string) $game['year']) ?>)</h2>
            <span class="rating" title="<?= htmlspecialchars((string) $game['rating']) ?> out of 5" data-rating="<?= htmlspecialchars((string)$game['rating']) ?>"></span>
        </li>
    <?php endforeach; ?>
</ul>

<h1 class="img-header">
    <img src="/img/gifs/ps2.gif" alt="PS2" />
    PlayStation 2
    <img src="/img/gifs/ps2.gif" alt="PS2" />
</h1>

<ul class="game-list">
    <?php foreach ($ps2Games as $game): ?>
        <li>
            <img src="<?= htmlspecialchars($game['cover']) ?>" alt="<?= htmlspecialchars($game['title']) ?>" class="disk" />
            <h2><?= htmlspecialchars($game['title']) ?> (<?= htmlspecialchars((string) $game['year']) ?>)</h2>
            <span class="rating" title="<?= htmlspecialchars((string) $game['rating']) ?> out of 5" data-rating="<?= htmlspecialchars((string)$game['rating']) ?>"></span>
        </li>
    <?php endforeach; ?>
</ul>

<h1 class="img-header">
    <img src="/img/gifs/snes.gif" alt="SNES" />
    Super Nintendo
    <img src="/img/gifs/snes.gif" alt="SNES" />
</h1>

<ul class="game-list">
    <?php foreach ($snesGames as $game): ?>
        <li>
            <img src="<?= htmlspecialchars($game['cover']) ?>" alt="<?= htmlspecialchars($game['title']) ?>" />
            <h2><?= htmlspecialchars($game['title']) ?> (<?= htmlspecialchars((string) $game['year']) ?>)</h2>
            <span class="rating" title="<?= htmlspecialchars((string) $game['rating']) ?> out of 5" data-rating="<?= htmlspecialchars((string)$game['rating']) ?>"></span>
        </li>
    <?php endforeach; ?>
</ul>

<h1 class="img-header">
    <img src="/img/gifs/gamecube.gif" alt="GameCube" />
    GameCube
    <img src="/img/gifs/gamecube.gif" alt="GameCube" />
</h1>

<ul class="game-list">
    <?php foreach ($gameCubeGames as $game): ?>
        <li>
            <img src="<?= htmlspecialchars($game['cover']) ?>" alt="<?= htmlspecialchars($game['title']) ?>" class="disk" />
            <h2><?= htmlspecialchars($game['title']) ?> (<?= htmlspecialchars((string) $game['year']) ?>)</h2>
            <span class="rating" title="<?= htmlspecialchars((string) $game['rating']) ?> out of 5" data-rating="<?= htmlspecialchars((string)$game['rating']) ?>"></span>
        </li>
    <?php endforeach; ?>
</ul>

<?php LayoutHelper::end(); ?>