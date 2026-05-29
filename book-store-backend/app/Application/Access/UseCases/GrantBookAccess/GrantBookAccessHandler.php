<?php

declare(strict_types=1);

namespace App\Application\Access\UseCases\GrantBookAccess;

use App\Domain\Access\Entities\UserBookAccess;
use App\Domain\Access\Interfaces\UserBookAccessRepositoryInterface;

final readonly class GrantBookAccessHandler
{
    public function __construct(
        private UserBookAccessRepositoryInterface $bookAccess,
    ) {}

    public function handle(GrantBookAccessCommand $command): void
    {
        if ($this->bookAccess->hasAccess($command->userId, $command->bookId)) {
            return;
        }

        $this->bookAccess->save(
            new UserBookAccess(
                id: null,
                userId: $command->userId,
                bookId: $command->bookId,
                grantedAt: new \DateTimeImmutable(),
                stripePaymentIntentId: $command->stripePaymentIntentId,
            ),
        );
    }
}
