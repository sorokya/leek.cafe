<?php

declare(strict_types=1);

namespace App;

enum Visibility: int
{
    case PRIVATE = 0;
    case PUBLIC = 1;
    case UNLISTED = 2;
}
