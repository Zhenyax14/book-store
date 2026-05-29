<?php

declare(strict_types=1);

namespace App\Domain\Reading\Enums;

enum ThemeEnum: string
{
    case LIGHT = 'light';
    case DARK  = 'dark';
}
