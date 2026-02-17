<?php

declare(strict_types=1);

use App\Utils\LayoutHelper;

LayoutHelper::assertRequestMethod('GET');
LayoutHelper::begin('Contact', 'Get in touch with me on leek.cafe!');
LayoutHelper::addStyleSheet('home.css');
?>

<div class="section">
    <div class="section-content" style="text-align: center;">
        <img src="/img/construction.gif" alt="Under Construction" />
        <p>
            This section is currently under construction. Please check back later for how to get in touch with me.
        </p>
    </div>
</div>

<?php LayoutHelper::end(); ?>