<?php

declare(strict_types=1);

namespace App;

enum ImageRole: int
{
    case INLINE = 0;
    case THUMBNAIL = 1;
    case COVER = 2;
}
