<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Domain\Notification\ValueObjects\NotificationContent;
use Illuminate\Mail\Mailable;
use App\Domain\Notification\Enums\NotificationTypeEnum;
use App\Infrastructure\Notification\Mails\BookFinishedMail;
use App\Infrastructure\Notification\Mails\BookPublishedMail;
use App\Infrastructure\Notification\Mails\PurchaseReceiptMail;
use App\Infrastructure\Notification\Mails\WelcomeMail;

final class NotificationMailFactory
{
    public static function make(NotificationContent $content): Mailable
    {
        return match ($content->type) {
            NotificationTypeEnum::WELCOME          => new WelcomeMail($content),
            NotificationTypeEnum::BOOK_FINISHED    => new BookFinishedMail($content),
            NotificationTypeEnum::BOOK_PUBLISHED   => new BookPublishedMail($content),
            NotificationTypeEnum::PURCHASE_RECEIPT => new PurchaseReceiptMail($content),
        };
    }
}
