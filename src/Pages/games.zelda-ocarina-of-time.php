<?php

declare(strict_types=1);

use App\Utils\LayoutHelper;

LayoutHelper::assertRequestMethod('GET');
LayoutHelper::begin('Ocarina of Time', 'Everything I want to talk about the best game ever made.');
LayoutHelper::addStyleSheet('oot.css');
?>

<div class="shrine">
    <div class="glow"></div>
    <div class="rays"></div>
    <img class="box" src="/img/games/oot-box.png" alt="Ocarina of Time Box Art" />
</div>

<?php LayoutHelper::end(); ?>