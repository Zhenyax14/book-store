<?php

namespace App\Domain\Dashboard\Enums;

enum StatCardTypeEnum: string
{
    case TODAY_REVENUE = 'today_revenue';
    case NEW_ORDERS = 'new_orders';
    case ACTIVE_READERS = 'active_readers';
    case ACTIVE_SUBSCRIPTIONS = 'active_subscriptions';
}
