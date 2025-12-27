<?php

namespace App\Models;

enum MediaStatus: int
{
    case Planned = 1;
    case InProgress = 2;
    case Completed = 3;
    case OnHold = 4;
    case Dropped = 5;
    // Future media statuses can be added here
}
