<?php

namespace Tests\Feature\Access;

use App\Application\Cart\Interfaces\PaymentGatewayInterface;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Fakes\FakePaymentGateway;
use Tests\TestCase;

final class SubscriptionCheckoutTest extends TestCase
{
    use DatabaseTransactions;

    private FakePaymentGateway $gateway;

    private UserModel          $user;

    private string             $token;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.stripe.subscription_price_ids' => [
            'price_monthly_eur',
            'price_annual_eur',
        ]]);

        $this->gateway = new FakePaymentGateway();
        $this->instance(PaymentGatewayInterface::class, $this->gateway);

        $this->user  = UserModel::factory()->create(['role' => 'reader']);
        $this->token = $this->user->createToken('reader-token')->plainTextToken;
    }

    public function test_returns_payment_url(): void
    {
        $this->withToken($this->token)
            ->postJson(route('subscriptions.checkout'), [
                'price_id' => 'price_monthly_eur',
            ])
            ->assertOk()
            ->assertJsonStructure(['payment_url']);
    }

    public function test_returns_409_when_subscription_already_active(): void
    {
        $this->gateway->pushWebhookEvent(
            FakePaymentGateway::makeSubscriptionEvent(
                userId: $this->user->id,
                stripeSubscriptionId: 'sub_exists',
                currentPeriodEnd: now()->addMonth()->timestamp,
            ),
        );
        $this->postJson(route('webhooks.stripe'));

        $this->withToken($this->token)
            ->postJson(route('subscriptions.checkout'), [
                'price_id' => 'price_monthly_eur',
            ])
            ->assertConflict();
    }

    public function test_rejects_invalid_price_id(): void
    {
        $this->withToken($this->token)
            ->postJson(route('subscriptions.checkout'), [
                'price_id' => 'price_unknown_xyz',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['price_id']);
    }

    public function test_requires_auth(): void
    {
        $this->postJson(route('subscriptions.checkout'), [
            'price_id' => 'price_monthly_eur',
        ])->assertUnauthorized();
    }
}
