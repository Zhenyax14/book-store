<?php

declare(strict_types=1);

namespace App\Domain\Notification\Enums;

enum NotificationTypeEnum: string
{
    case WELCOME           = 'welcome';
    case BOOK_PUBLISHED    = 'book_published';
    case BOOK_FINISHED     = 'book_finished';
    case PURCHASE_RECEIPT  = 'purchase_receipt';
}
