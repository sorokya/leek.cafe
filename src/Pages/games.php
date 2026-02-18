<?php

declare(strict_types=1);

use App\Utils\LayoutHelper;

LayoutHelper::assertRequestMethod('GET');
LayoutHelper::begin('Games', 'Explore my favorite games on leek.cafe!');
LayoutHelper::addStyleSheet('games.css');
?>

<?php LayoutHelper::addMusic('/music/lostwoods.webm'); ?>

<?php LayoutHelper::end(); ?>