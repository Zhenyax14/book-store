<?php

declare(strict_types=1);

namespace Tests\Feature\Cart;

use App\Application\Cart\Interfaces\PaymentGatewayInterface;
use App\Domain\Cart\Enums\CartItemTypeEnum;
use App\Domain\Shared\Enums\RoleEnum;
use App\Infrastructure\Persistence\Models\BookModel;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Fakes\FakePaymentGateway;
use Tests\TestCase;

final class CartTest extends TestCase
{
    use DatabaseTransactions;

    private UserModel $user;

    private string $token;

    private BookModel $book;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instance(PaymentGatewayInterface::class, new FakePaymentGateway());

        $this->user = UserModel::factory()->create(['role' => RoleEnum::READER]);
        $this->token = $this->user->createToken('reader-token')->plainTextToken;
        $this->book = BookModel::factory()->create([
            'price' => 1990,
            'currency' => 'EUR',
        ]);
    }

    public function test_show_returns_null_when_no_cart(): void
    {
        $this->withToken($this->token)
            ->getJson(route('cart.show'))
            ->assertOk()
            ->assertExactJson(['data' => null]);
    }

    public function test_show_returns_cart_structure(): void
    {
        $this->withToken($this->token)
            ->postJson(route('cart.items.add'), [
                'type' => CartItemTypeEnum::BOOK->value,
                'reference_id' => $this->book->id,
            ]);

        $this->withToken($this->token)
            ->getJson(route('cart.show'))
            ->assertOk()
            ->assertJsonStructure([
                'id', 'status', 'items_count', 'created_at',
                'items' => [['type', 'reference_id', 'title', 'price']],
                'total' => ['amount', 'currency', 'formatted'],
            ]);
    }

    public function test_show_requires_auth(): void
    {
        $this->getJson(route('cart.show'))->assertUnauthorized();
    }

    public function test_add_item_creates_cart_and_returns_it(): void
    {
        $this->withToken($this->token)
            ->postJson(route('cart.items.add'), [
                'type' => CartItemTypeEnum::BOOK->value,
                'reference_id' => $this->book->id,
            ])
            ->assertOk()
            ->assertJsonFragment([
                'type' => 'book',
                'reference_id' => $this->book->id,
            ])
            ->assertJsonPath('items_count', 1);
    }

    public function test_add_item_persists_to_database(): void
    {
        $this->withToken($this->token)
            ->postJson(route('cart.items.add'), [
                'type' => CartItemTypeEnum::BOOK->value,
                'reference_id' => $this->book->id,
            ]);

        $this->assertDatabaseHas('cart_items', [
            'type' => 'book',
            'reference_id' => $this->book->id,
        ]);
    }

    public function test_add_same_item_twice_returns_conflict(): void
    {
        $this->withToken($this->token)
            ->postJson(route('cart.items.add'), [
                'type' => CartItemTypeEnum::BOOK->value,
                'reference_id' => $this->book->id,
            ]);

        $this->withToken($this->token)
            ->postJson(route('cart.items.add'), [
                'type' => CartItemTypeEnum::BOOK->value,
                'reference_id' => $this->book->id,
            ])
            ->assertConflict();
    }

    public function test_add_item_validates_type(): void
    {
        $this->withToken($this->token)
            ->postJson(route('cart.items.add'), [
                'type' => 'invalid',
                'reference_id' => 1,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['type']);
    }

    public function test_add_item_validates_reference_id(): void
    {
        $this->withToken($this->token)
            ->postJson(route('cart.items.add'), [
                'type' => CartItemTypeEnum::BOOK->value,
                'reference_id' => 0,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['reference_id']);
    }

    public function test_add_nonexistent_book_returns_404(): void
    {
        $this->withToken($this->token)
            ->postJson(route('cart.items.add'), [
                'type' => CartItemTypeEnum::BOOK->value,
                'reference_id' => 99999,
            ])
            ->assertNotFound();
    }

    public function test_add_item_requires_auth(): void
    {
        $this->postJson(route('cart.items.add'), [
            'type' => CartItemTypeEnum::BOOK->value,
            'reference_id' => $this->book->id,
        ])->assertUnauthorized();
    }

    public function test_remove_item_returns_updated_cart(): void
    {
        $this->withToken($this->token)
            ->postJson(route('cart.items.add'), [
                'type' => CartItemTypeEnum::BOOK->value,
                'reference_id' => $this->book->id,
            ]);

        $this->withToken($this->token)
            ->deleteJson(route('cart.items.remove', [
                'type' => CartItemTypeEnum::BOOK->value,
                'referenceId' => $this->book->id,
            ]))
            ->assertOk()
            ->assertJsonPath('items_count', 0);
    }

    public function test_remove_item_deletes_from_database(): void
    {
        $this->withToken($this->token)
            ->postJson(route('cart.items.add'), [
                'type' => CartItemTypeEnum::BOOK->value,
                'reference_id' => $this->book->id,
            ]);

        $this->withToken($this->token)
            ->deleteJson(route('cart.items.remove', [
                'type' => CartItemTypeEnum::BOOK->value,
                'referenceId' => $this->book->id,
            ]));

        $this->assertDatabaseMissing('cart_items', [
            'type' => 'book',
            'reference_id' => $this->book->id,
        ]);
    }

    public function test_remove_nonexistent_item_returns_404(): void
    {
        $this->withToken($this->token)
            ->postJson(route('cart.items.add'), [
                'type' => CartItemTypeEnum::BOOK->value,
                'reference_id' => $this->book->id,
            ]);

        $this->withToken($this->token)
            ->deleteJson(route('cart.items.remove', [
                'type' => CartItemTypeEnum::BOOK->value,
                'referenceId' => 99999,
            ]))
            ->assertNotFound();
    }

    public function test_checkout_returns_payment_url(): void
    {
        $this->withToken($this->token)
            ->postJson(route('cart.items.add'), [
                'type' => CartItemTypeEnum::BOOK->value,
                'reference_id' => $this->book->id,
            ]);

        $this->withToken($this->token)
            ->postJson(route('cart.checkout'), ['currency' => 'EUR'])
            ->assertOk()
            ->assertJsonStructure(['cart_id', 'payment_url', 'total'])
            ->assertJsonFragment(['amount' => 1990]);
    }

    public function test_checkout_marks_cart_as_checked_out_in_database(): void
    {
        $this->withToken($this->token)
            ->postJson(route('cart.items.add'), [
                'type' => CartItemTypeEnum::BOOK->value,
                'reference_id' => $this->book->id,
            ]);

        $this->withToken($this->token)
            ->postJson(route('cart.checkout'), ['currency' => 'EUR']);

        $this->assertDatabaseHas('carts', [
            'user_id' => $this->user->id,
            'status' => 'checked_out',
        ]);
    }

    public function test_checkout_returns_404_when_no_cart(): void
    {
        $this->withToken($this->token)
            ->postJson(route('cart.checkout'), ['currency' => 'EUR'])
            ->assertNotFound();
    }

    public function test_checkout_validates_currency(): void
    {
        $this->withToken($this->token)
            ->postJson(route('cart.checkout'), ['currency' => 'GBP'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['currency']);
    }

    public function test_checkout_requires_auth(): void
    {
        $this->postJson(route('cart.checkout'), ['currency' => 'EUR'])
            ->assertUnauthorized();
    }
}
