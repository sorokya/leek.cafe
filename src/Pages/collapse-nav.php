<?php

declare(strict_types=1);

use App\Utils\LayoutHelper;
use App\Utils\ResponseHelper;
use App\Utils\SessionHelper;

LayoutHelper::assertRequestMethod('POST');

$currentCollapsedState = SessionHelper::getBool('primary_nav_collapsed');
SessionHelper::setBool('primary_nav_collapsed', !$currentCollapsedState);

ResponseHelper::back();
