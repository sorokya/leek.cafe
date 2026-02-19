<?php

declare(strict_types=1);

use App\Utils\LayoutHelper;

LayoutHelper::begin('404 Not Found', 'The page you are looking for does not exist.'); ?>

<h2>404 Not Found</h2>
<p>Sorry, the page you are looking for does not exist.</p>
<p>Click <a href="<?= $_SERVER['HTTP_REFERER'] ?? '/' ?>">here</a> to go back.</p>

<?php LayoutHelper::end(); ?>