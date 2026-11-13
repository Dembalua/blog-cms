<?php

namespace App\Enums;

enum RedirectStatus: int
{
    case MOVED_PERMANENTLY = 301;
    case GONE = 410;
}
