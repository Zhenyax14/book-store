<?php

declare(strict_types=1);

namespace App\Domain\Cart\Entities;

use App\Domain\Cart\Enums\CartItemTypeEnum;
use App\Domain\Cart\Enums\CartStatusEnum;
use App\Domain\Cart\Exceptions\CartAlreadyCheckedOutException;
use App\Domain\Cart\Exceptions\CartItemAlreadyExistsException;
use App\Domain\Cart\Exceptions\CartItemNotFoundException;
use App\Domain\Cart\ValueObjects\CartId;
use App\Domain\Shared\ValueObjects\Currency;
use App\Domain\Shared\ValueObjects\Money;
use DateTimeImmutable;

/**
 * @property CartId $id
 * @property int $user_id
 * @property CartStatusEnum $status
 * @property CartItem[] $items
 * @property DateTimeImmutable $created_at
 * @property DateTimeImmutable|null $checked_out_at
 */
final readonly class Cart
{
    /** @param CartItem[] $items */
    public function __construct(
        public ?CartId            $id,
        public int                $userId,
        public CartStatusEnum     $status,
        /** @var CartItem[] */
        public array              $items,
        public DateTimeImmutable  $createdAt,
        public ?DateTimeImmutable $checkedOutAt,
    ) {}

    public static function create(int $userId): self
    {
        return new self(
            id: null,
            userId: $userId,
            status: CartStatusEnum::ACTIVE,
            items: [],
            createdAt: new DateTimeImmutable(),
            checkedOutAt: null,
        );
    }

    public function addItem(CartItem $item): self
    {
        $this->assertActive();
        $this->assertItemNotExists($item->type, $item->referenceId);

        return new self(
            id: $this->id,
            userId: $this->userId,
            status: $this->status,
            items: [...$this->items, $item],
            createdAt: $this->createdAt,
            checkedOutAt: $this->checkedOutAt,
        );
    }

    public function removeItem(CartItemTypeEnum $type, int $referenceId): self
    {
        $this->assertActive();

        $filtered = array_values(
            array_filter(
                $this->items,
                static fn(CartItem $i) => ! ($i->type === $type && $i->referenceId === $referenceId),
            ),
        );

        if (count($filtered) === count($this->items)) {
            throw new CartItemNotFoundException($type, $referenceId);
        }

        return new self(
            id: $this->id,
            userId: $this->userId,
            status: $this->status,
            items: $filtered,
            createdAt: $this->createdAt,
            checkedOutAt: $this->checkedOutAt,
        );
    }

    public function checkout(): self
    {
        $this->assertActive();
        $this->assertNotEmpty();

        return new self(
            id: $this->id,
            userId: $this->userId,
            status: CartStatusEnum::CHECKED_OUT,
            items: $this->items,
            createdAt: $this->createdAt,
            checkedOutAt: new DateTimeImmutable(),
        );
    }

    public function clear(): self
    {
        $this->assertActive();

        return new self(
            id: $this->id,
            userId: $this->userId,
            status: $this->status,
            items: [],
            createdAt: $this->createdAt,
            checkedOutAt: $this->checkedOutAt,
        );
    }

    public function total(Currency $currency): Money
    {
        return array_reduce(
            $this->items,
            static fn(Money $carry, CartItem $item) => $carry->add($item->price),
            Money::zero($currency),
        );
    }

    public function isEmpty(): bool
    {
        return [] === $this->items;
    }

    public function isActive(): bool
    {
        return CartStatusEnum::ACTIVE === $this->status;
    }

    public function containsItem(CartItemTypeEnum $type, int $referenceId): bool
    {
        return array_any($this->items, fn($item) => $item->type === $type && $item->referenceId === $referenceId);
    }

    private function assertActive(): void
    {
        if ( ! $this->isActive()) {
            throw new CartAlreadyCheckedOutException();
        }
    }

    private function assertNotEmpty(): void
    {
        if ($this->isEmpty()) {
            throw new \DomainException('Cannot checkout an empty cart.');
        }
    }

    private function assertItemNotExists(CartItemTypeEnum $type, int $referenceId): void
    {
        if ($this->containsItem($type, $referenceId)) {
            throw new CartItemAlreadyExistsException($type, $referenceId);
        }
    }
}
