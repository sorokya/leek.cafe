<?php

namespace App\Models;

enum ContentType: int
{
    case Post = 1;
    case Media = 2;
    // Future content types can be added here
}
