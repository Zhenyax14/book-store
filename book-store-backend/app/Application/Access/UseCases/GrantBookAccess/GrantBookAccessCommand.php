<?php

declare(strict_types=1);

namespace App\Application\Access\UseCases\GrantBookAccess;

final readonly class GrantBookAccessCommand
{
    public function __construct(
        public int     $userId,
        public int     $bookId,
        public ?string $stripePaymentIntentId = null,
    ) {}
}
