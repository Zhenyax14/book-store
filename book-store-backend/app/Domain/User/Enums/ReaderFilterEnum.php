<?php

declare(strict_types=1);

namespace App\Domain\User\Enums;

enum ReaderFilterEnum: string
{
    case SUBSCRIBED     = 'subscribed';
    case NOT_SUBSCRIBED = 'not_subscribed';
    case HAS_BOOKS     = 'has_books';
    case HAS_NOT_BOOKS = 'has_not_books';

    public function isSubscriptionFilter(): bool
    {
        return in_array($this, [self::SUBSCRIBED, self::NOT_SUBSCRIBED]);
    }

    public function isBooksFilter(): bool
    {
        return in_array($this, [self::HAS_BOOKS, self::HAS_NOT_BOOKS]);
    }
}
