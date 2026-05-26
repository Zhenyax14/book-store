<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment;

use App\Application\Cart\Interfaces\PaymentGatewayInterface;
use App\Domain\Shared\ValueObjects\Money;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

final readonly class StripePaymentGateway implements PaymentGatewayInterface
{
    public function __construct(
        private StripeClient $stripe,
        private string       $webhookSecret,
        private string       $successUrl,
        private string       $cancelUrl,
    ) {}

    /**
     * @throws ApiErrorException
     */
    public function createSession(int $cartId, Money $amount, array $metadata = []): string
    {
        try {
            $payloadMetadata = array_merge($metadata, ['cart_id' => $cartId]);

            $session = $this->stripe->checkout->sessions->create(
                [
                    'mode' => 'payment',
                    'line_items' => [[
                        'price_data' => [
                            'currency'     => mb_strtolower($amount->currency->code),
                            'unit_amount'  => $amount->amount,
                            'product_data' => [
                                'name' => 'Book Store Order #' . $cartId,
                            ],
                        ],
                        'quantity' => 1,
                    ]],
                    'payment_intent_data' => [
                        'metadata' => $payloadMetadata,
                    ],
                    'metadata'    => $payloadMetadata,
                    'success_url' => $this->successUrl . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url'  => $this->cancelUrl,
                    'expires_at'  => time() + 3600,
                ],
                [
                    'idempotency_key' => 'checkout_session_cart_' . $cartId,
                ],
            );

            return $session->url;
        } catch (ApiErrorException $e) {
            Log::error('Stripe checkout session creation failed', [
                'error' => $e->getMessage(),
                'metadata' => $metadata,
                'cartId' => $cartId,
                'amount' => $amount->toArray(),
                'payload' => $e->getJsonBody(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * @throws ApiErrorException
     */
    public function createSubscriptionSession(
        int    $userId,
        string $priceId,
    ): string {
        try {
            $session = $this->stripe->checkout->sessions->create(
                [
                    'mode'      => 'subscription',
                    'line_items' => [[
                        'price'    => $priceId,
                        'quantity' => 1,
                    ]],
                    'subscription_data' => [
                        'metadata' => ['user_id' => $userId],
                    ],
                    'metadata'    => ['user_id' => $userId],
                    'success_url' => $this->successUrl,
                    'cancel_url'  => $this->cancelUrl,
                ],
                [
                    'idempotency_key' => "subscription_session_user_{$userId}_{$priceId}_" . time(),
                ],
            );

            return $session->url;
        } catch (ApiErrorException $e) {
            Log::error('Stripe checkout session creation failed', [
                'error' => $e->getMessage(),
                'userId' => $userId,
                'stripePriceId' => $priceId,
                'payload' => $e->getJsonBody(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * @throws SignatureVerificationException
     */
    public function constructWebhookEvent(string $payload, string $signature): object
    {
        return Webhook::constructEvent($payload, $signature, $this->webhookSecret);
    }
}
