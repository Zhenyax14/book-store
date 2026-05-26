<?php

namespace App\Domain\Catalog\Enums;

enum AccessTypeEnum: string
{
    case SUBSCRIPTION = 'subscription';
    case PURCHASE     = 'purchase';
    case FREE         = 'free';
}
