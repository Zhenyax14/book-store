<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Domain\Cart\Enums\CartItemTypeEnum;
use App\Domain\Cart\Interfaces\CartItemPriceResolverInterface;
use App\Domain\Shared\ValueObjects\Money;

final class FakeCartItemPriceResolver implements CartItemPriceResolverInterface
{
    /** @var array<string, array{title: string, price: Money}> */
    private array $map = [];

    public function register(CartItemTypeEnum $type, int $referenceId, string $title, Money $price): void
    {
        $this->map[$this->key($type, $referenceId)] = compact('title', 'price');
    }

    public function resolve(CartItemTypeEnum $type, int $referenceId): array
    {
        $key = $this->key($type, $referenceId);

        return $this->map[$key]
            ?? throw new \InvalidArgumentException("No price registered for {$key}");
    }

    private function key(CartItemTypeEnum $type, int $referenceId): string
    {
        return $type->value . ':' . $referenceId;
    }
}
