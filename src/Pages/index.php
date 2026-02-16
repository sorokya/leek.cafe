<?php

declare(strict_types=1);

use App\Utils\LayoutHelper;

LayoutHelper::assertRequestMethod('GET');
LayoutHelper::begin('Home');
?>

<h2>Under Construction!</h2>
<p>
    This site is currently under construction. Please check back later for updates!
    <img src="/img/construction.gif" alt="Under Construction" />
</p>

<?php LayoutHelper::end();
