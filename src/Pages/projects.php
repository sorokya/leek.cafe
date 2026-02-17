<?php

declare(strict_types=1);

use App\Utils\LayoutHelper;

LayoutHelper::assertRequestMethod('GET');
LayoutHelper::begin('Projects', 'A showcase of my projects on leek.cafe!');
LayoutHelper::addStyleSheet('home.css');
?>

<div class="section">
    <div class="section-content" style="text-align: center;">
        <img src="/img/construction.gif" alt="Under Construction" />
        <p>
            This section is currently under construction. Please check back later for updates on my projects.
        </p>
    </div>
</div>

<?php LayoutHelper::end(); ?>