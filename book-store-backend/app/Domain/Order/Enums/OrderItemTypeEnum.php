<?php

declare(strict_types=1);

namespace App\Domain\Order\Enums;

enum OrderItemTypeEnum: string
{
    case BOOK = 'book';
    case SUBSCRIPTION = 'subscription';
}
