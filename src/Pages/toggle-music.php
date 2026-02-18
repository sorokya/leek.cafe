<?php

declare(strict_types=1);

use App\Utils\LayoutHelper;
use App\Utils\ResponseHelper;
use App\Utils\SessionHelper;

LayoutHelper::assertRequestMethod('POST');

$currentMusicState = SessionHelper::getBool('auto_play_enabled');
SessionHelper::setBool('auto_play_enabled', !$currentMusicState);

ResponseHelper::back();
