<?php

declare(strict_types=1);

namespace App\Domain\Cart\Enums;

enum CartStatusEnum: string
{
    case ACTIVE    = 'active';
    case CHECKED_OUT = 'checked_out';
    case ABANDONED = 'abandoned';
}
