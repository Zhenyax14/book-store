<?php

declare(strict_types=1);

namespace App\Domain\Cart\Enums;

enum CartItemTypeEnum: string
{
    case BOOK         = 'book';
    case SUBSCRIPTION = 'subscription';
}
