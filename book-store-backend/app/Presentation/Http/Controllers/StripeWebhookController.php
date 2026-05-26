<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Access\UseCases\GrantBookAccess\GrantBookAccessCommand;
use App\Application\Access\UseCases\GrantBookAccess\GrantBookAccessHandler;
use App\Application\Access\UseCases\GrantSubscription\GrantSubscriptionCommand;
use App\Application\Access\UseCases\GrantSubscription\GrantSubscriptionHandler;
use App\Application\Cart\Interfaces\PaymentGatewayInterface;
use App\Application\Shared\Interfaces\EventDispatcherInterface;
use App\Domain\Cart\Enums\CartItemTypeEnum;
use App\Domain\Cart\Interfaces\CartRepositoryInterface;
use App\Domain\Order\Events\PurchaseCompleted;
use DateMalformedStringException;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;

final class StripeWebhookController extends Controller
{
    public function __construct(
        private readonly PaymentGatewayInterface  $gateway,
        private readonly GrantBookAccessHandler   $grantBookAccess,
        private readonly GrantSubscriptionHandler $grantSubscription,
        private readonly CartRepositoryInterface  $carts,
        private readonly EventDispatcherInterface $dispatcher,
    ) {}

    /**
     * @throws DateMalformedStringException
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $event = $this->gateway->constructWebhookEvent(
                $request->getContent(),
                $request->header('Stripe-Signature', ''),
            );
        } catch (SignatureVerificationException) {
            return new JsonResponse(['message' => 'Invalid signature.'], 400);
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($event->data->object),
            'customer.subscription.created',
            'customer.subscription.updated' => $this->handleSubscriptionUpdate($event->data->object),
            default => null,
        };

        return new JsonResponse(['received' => true]);
    }

    private function handleCheckoutCompleted(object $session): void
    {
        $userId = (int) ($session->metadata?->user_id ?? 0);
        $cartId = (int) ($session->metadata?->cart_id ?? 0);

        if (0 === $userId || 0 === $cartId) {
            Log::warning('Stripe webhook: missing metadata', ['session_id' => $session->id]);

            return;
        }

        $cart = $this->carts->findById($cartId);

        if (null === $cart) {
            Log::warning('Stripe webhook: cart not found', ['cart_id' => $cartId]);

            return;
        }

        $paymentIntentId = $session->payment_intent ?? null;

        foreach ($cart->items as $item) {
            if (CartItemTypeEnum::BOOK !== $item->type) {
                continue;
            }

            $this->grantBookAccess->handle(
                new GrantBookAccessCommand(
                    userId: $userId,
                    bookId: $item->referenceId,
                    stripePaymentIntentId: $paymentIntentId,
                ),
            );

            $this->dispatcher->dispatch(new PurchaseCompleted(
                userId: $userId,
                bookId: $item->referenceId,
                bookTitle: $item->title,
            ));
        }
    }

    /**
     * @throws DateMalformedStringException
     */
    private function handleSubscriptionUpdate(object $subscription): void
    {
        $userId = (int) ($subscription->metadata?->user_id ?? 0);

        if (0 === $userId) {
            Log::warning('Stripe webhook: subscription missing user_id', [
                'subscription_id' => $subscription->id,
            ]);

            return;
        }

        $this->grantSubscription->handle(new GrantSubscriptionCommand(
            userId: $userId,
            stripeSubscriptionId: $subscription->id,
            expiresAt: new DateTimeImmutable('@' . $subscription->current_period_end),
        ));
    }
}
