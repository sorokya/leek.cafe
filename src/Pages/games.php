<?php

declare(strict_types=1);

use App\Utils\LayoutHelper;

LayoutHelper::assertRequestMethod('GET');
LayoutHelper::begin('Games', 'Explore my favorite games on leek.cafe!');
LayoutHelper::addStyleSheet('games.css');
?>

<?php LayoutHelper::addMusic('/music/astral-observatory.mp3', 'audio/mpeg'); ?>

<?php LayoutHelper::end(); ?>