<?php

namespace App\Models;

enum MediaType: int
{
    case Film = 1;
    case Series = 2;
    case Music = 3;
    case Book = 4;
    case Anime = 5;
    case Manga = 6;
    case Game = 7;
    // Future media types can be added here
}
