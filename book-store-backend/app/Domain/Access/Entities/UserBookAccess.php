<?php

declare(strict_types=1);

namespace App\Domain\Access\Entities;

use DateTimeImmutable;

final readonly class UserBookAccess
{
    public function __construct(
        public ?int              $id,
        public int               $userId,
        public int               $bookId,
        public DateTimeImmutable $grantedAt,
        public ?string           $stripePaymentIntentId,
    ) {}
}
