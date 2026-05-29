<?php

declare(strict_types=1);

namespace Tests\Feature\Payment;

use App\Application\Cart\Interfaces\PaymentGatewayInterface;
use App\Domain\Access\Interfaces\UserBookAccessRepositoryInterface;
use App\Domain\Access\Interfaces\UserSubscriptionAccessRepositoryInterface;
use App\Domain\Cart\Entities\Cart;
use App\Domain\Cart\Entities\CartItem;
use App\Domain\Cart\Enums\CartItemTypeEnum;
use App\Domain\Cart\Interfaces\CartRepositoryInterface;
use App\Domain\Shared\ValueObjects\Currency;
use App\Domain\Shared\ValueObjects\Money;
use App\Infrastructure\Persistence\Models\BookModel;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Fakes\FakePaymentGateway;
use Tests\TestCase;

final class StripeWebhookControllerTest extends TestCase
{
    use DatabaseTransactions;

    private FakePaymentGateway $gateway;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gateway = new FakePaymentGateway();
        $this->instance(PaymentGatewayInterface::class, $this->gateway);
    }

    public function test_returns_400_when_signature_invalid(): void
    {
        $this->gateway->failNextWebhook();

        $this->postJson(route('webhooks.stripe'), [], ['Stripe-Signature' => 'bad'])
            ->assertStatus(400)
            ->assertJsonFragment(['message' => 'Invalid signature.']);
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_grants_book_access_on_checkout_completed(): void
    {
        /** @var UserModel $user */
        $user = UserModel::factory()->create();
        /** @var BookModel $book */
        $book = BookModel::factory()->create(['price' => 1990, 'currency' => 'EUR']);

        $cart = $this->createCheckedOutCart($user->id, [$book]);

        $this->gateway->pushWebhookEvent(
            FakePaymentGateway::makeCheckoutEvent(
                userId: $user->id,
                cartId: $cart->id->value,
            ),
        );

        $this->postJson(route('webhooks.stripe'))
            ->assertOk()
            ->assertExactJson(['received' => true]);

        $this->assertTrue(
            $this->app->make(UserBookAccessRepositoryInterface::class)
                ->hasAccess($user->id, $book->id),
        );
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_grants_access_to_multiple_books(): void
    {
        /** @var UserModel $user */
        $user  = UserModel::factory()->create();
        /** @var BookModel $bookA */
        $bookA = BookModel::factory()->create(['price' => 1990, 'currency' => 'EUR']);
        /** @var BookModel $bookB */
        $bookB = BookModel::factory()->create(['price' => 2490, 'currency' => 'EUR']);

        $cart = $this->createCheckedOutCart($user->id, [$bookA, $bookB]);

        $this->gateway->pushWebhookEvent(
            FakePaymentGateway::makeCheckoutEvent(
                userId: $user->id,
                cartId: $cart->id->value,
            ),
        );

        $this->postJson(route('webhooks.stripe'))->assertOk();

        $repo = $this->app->make(UserBookAccessRepositoryInterface::class);

        $this->assertTrue($repo->hasAccess($user->id, $bookA->id));
        $this->assertTrue($repo->hasAccess($user->id, $bookB->id));
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_checkout_is_idempotent(): void
    {
        /** @var UserModel $user */
        $user = UserModel::factory()->create();
        /** @var BookModel $book */
        $book = BookModel::factory()->create(['price' => 1990, 'currency' => 'EUR']);

        $cart = $this->createCheckedOutCart($user->id, [$book]);

        $event = FakePaymentGateway::makeCheckoutEvent(
            userId: $user->id,
            cartId: $cart->id->value,
        );

        $this->gateway->pushWebhookEvent($event);
        $this->postJson(route('webhooks.stripe'))->assertOk();

        $this->gateway->pushWebhookEvent($event);
        $this->postJson(route('webhooks.stripe'))->assertOk();

        $this->assertDatabaseCount('user_book_access', 1);
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_checkout_without_book_items_does_not_grant_access(): void
    {
        /** @var UserModel $user */
        $user = UserModel::factory()->create();

        $cart = $this->createCheckedOutCartWithSubscription($user->id);

        $this->gateway->pushWebhookEvent(
            FakePaymentGateway::makeCheckoutEvent(
                userId: $user->id,
                cartId: $cart->id->value,
            ),
        );

        $this->postJson(route('webhooks.stripe'))->assertOk();

        $this->assertDatabaseEmpty('user_book_access');
    }

    public function test_checkout_ignores_missing_cart(): void
    {
        /** @var UserModel $user */
        $user = UserModel::factory()->create();

        $this->gateway->pushWebhookEvent(
            FakePaymentGateway::makeCheckoutEvent(
                userId: $user->id,
                cartId: 99999,
            ),
        );

        $this->postJson(route('webhooks.stripe'))->assertOk();

        $this->assertDatabaseEmpty('user_book_access');
    }

    public function test_checkout_ignores_missing_metadata(): void
    {
        $event = (object) [
            'type' => 'checkout.session.completed',
            'data' => (object) [
                'object' => (object) [
                    'id'       => 'cs_test_no_meta',
                    'metadata' => null,
                ],
            ],
        ];

        $this->gateway->pushWebhookEvent($event);

        $this->postJson(route('webhooks.stripe'))->assertOk();

        $this->assertDatabaseEmpty('user_book_access');
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_grants_subscription_on_subscription_created(): void
    {
        /** @var UserModel $user */
        $user = UserModel::factory()->create();

        $this->gateway->pushWebhookEvent(
            FakePaymentGateway::makeSubscriptionEvent(
                userId: $user->id,
                stripeSubscriptionId: 'sub_test_123',
                currentPeriodEnd: now()->addMonth()->timestamp,
            ),
        );

        $this->postJson(route('webhooks.stripe'))->assertOk();

        $subscription = $this->app->make(UserSubscriptionAccessRepositoryInterface::class)
            ->findActiveByUser($user->id);

        $this->assertNotNull($subscription);
        $this->assertSame('sub_test_123', $subscription->stripeSubscriptionId);
    }

    public function test_subscription_grant_is_idempotent(): void
    {
        /** @var UserModel $user */
        $user = UserModel::factory()->create();

        $event = FakePaymentGateway::makeSubscriptionEvent(
            userId: $user->id,
            stripeSubscriptionId: 'sub_idem',
            currentPeriodEnd: now()->addMonth()->timestamp,
        );

        $this->gateway->pushWebhookEvent($event);
        $this->postJson(route('webhooks.stripe'))->assertOk();

        $this->gateway->pushWebhookEvent($event);
        $this->postJson(route('webhooks.stripe'))->assertOk();

        $this->assertDatabaseCount('user_subscriptions', 1);
    }

    public function test_unknown_event_type_returns_200(): void
    {
        $this->gateway->pushWebhookEvent((object) [
            'type' => 'payment_intent.created',
            'data' => (object) ['object' => (object) []],
        ]);

        $this->postJson(route('webhooks.stripe'))->assertOk();
    }

    /**
     * Создаёт корзину в БД и переводит её в статус checked_out.
     *
     * @param BookModel[] $books
     * @throws BindingResolutionException
     */
    private function createCheckedOutCart(int $userId, array $books): Cart
    {
        /** @var CartRepositoryInterface $repo */
        $repo = $this->app->make(CartRepositoryInterface::class);

        $cart = Cart::create($userId);

        foreach ($books as $book) {
            $cart = $cart->addItem(new CartItem(
                id: null,
                cartId: 0,
                type: CartItemTypeEnum::BOOK,
                referenceId: $book->id,
                title: $book->title,
                price: new Money($book->price, new Currency($book->currency)),
            ));
        }

        return $repo->save($cart->checkout());
    }

    /**
     * @throws BindingResolutionException
     */
    private function createCheckedOutCartWithSubscription(int $userId): Cart
    {
        /** @var CartRepositoryInterface $repo */
        $repo = $this->app->make(CartRepositoryInterface::class);

        $cart = Cart::create($userId);
        $cart = $cart->addItem(new CartItem(
            id: null,
            cartId: 0,
            type: CartItemTypeEnum::SUBSCRIPTION,
            referenceId: 1,
            title: 'Monthly Subscription',
            price: Money::ofEur(990),
        ));

        return $repo->save($cart->checkout());
    }
}
