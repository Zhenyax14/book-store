<?php

declare(strict_types=1);

namespace App\Application\Cart\Services;

use App\Domain\Cart\Enums\CartItemTypeEnum;
use App\Domain\Cart\Interfaces\CartItemPriceResolverInterface;
use App\Domain\Catalog\Exceptions\BookNotFoundException;
use App\Domain\Catalog\Interfaces\BookRepositoryInterface;
use App\Domain\Shared\ValueObjects\Money;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

final readonly class CartItemPriceResolver implements CartItemPriceResolverInterface
{
    public function __construct(
        private BookRepositoryInterface $books,
    ) {}

    public function resolve(CartItemTypeEnum $type, int $referenceId): array
    {
        return match ($type) {
            CartItemTypeEnum::BOOK         => $this->resolveBook($referenceId),
            CartItemTypeEnum::SUBSCRIPTION => $this->resolveSubscription($referenceId),
        };
    }

    private function resolveBook(int $bookId): array
    {
        $book = $this->books->findById($bookId)
            ?? throw new BookNotFoundException($bookId);

        return ['title' => $book->title, 'price' => $book->price];
    }

    private function resolveSubscription(int $planId): array
    {
        //TODO SubscriptionPlanRepositoryInterface
        $plans = [
            1 => ['title' => 'Monthly Subscription', 'price' => Money::ofEur(990)],
            2 => ['title' => 'Annual Subscription',  'price' => Money::ofEur(7900)],
        ];

        return $plans[$planId]
            ?? throw new InvalidArgumentException("Subscription plan #{$planId} not found.", Response::HTTP_NOT_FOUND);
    }
}
