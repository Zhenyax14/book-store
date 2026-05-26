<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Access\UseCases\InitiateSubscription\InitiateSubscriptionHandler;
use App\Presentation\Http\Requests\Subscription\InitiateSubscriptionRequest;
use Illuminate\Http\JsonResponse;
use App\Application\Access\UseCases\InitiateSubscription\InitiateSubscriptionCommand;

final class SubscriptionCheckoutController extends Controller
{
    public function __construct(
        private readonly InitiateSubscriptionHandler $handler,
    ) {}

    public function __invoke(InitiateSubscriptionRequest $request): JsonResponse
    {
        $paymentUrl = $this->handler->handle(
            new InitiateSubscriptionCommand(
                userId: $request->user()->id,
                priceId: $request->validated('price_id'),
            ),
        );

        return new JsonResponse(['payment_url' => $paymentUrl]);
    }
}
