<?php

declare(strict_types=1);

namespace App\Domain\Reading\Enums;

enum PaginationModeEnum: string
{
    case PAGE = 'page';
    case SCROLL  = 'scroll';
}
