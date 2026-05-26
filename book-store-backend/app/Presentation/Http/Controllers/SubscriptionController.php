<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Subscription\UseCases\GetSubscription\GetSubscriptionCommand;
use App\Application\Subscription\UseCases\GetSubscription\GetSubscriptionHandler;
use App\Application\Subscription\UseCases\ListSubscriptions\ListSubscriptionsCommand;
use App\Application\Subscription\UseCases\ListSubscriptions\ListSubscriptionsHandler;
use App\Presentation\Http\Requests\Subscription\ListSubscriptionsRequest;
use App\Presentation\Http\Resources\Subscription\SubscriptionCollectionResource;
use App\Presentation\Http\Resources\Subscription\SubscriptionResource;
use Illuminate\Http\JsonResponse;

final class SubscriptionController extends Controller
{
    public function __construct(
        private readonly ListSubscriptionsHandler $handler,
        private readonly GetSubscriptionHandler $getHandler,
    ) {}

    /**
     * @param ListSubscriptionsRequest $request
     * @return JsonResponse
     * @response array{
     *     data: array<int, array{
     *         id: int,
     *         status: App\Domain\Shared\Enums\SubscriptionStatusEnum,
     *         user_id: int,
     *         expired_at: string,
     *         started_at: string,
     *         stripe_subscription_id: string
     *     }>,
     *     meta: array{
     *          total: int,
     *          per_page: int,
     *          current_page: int,
     *          total_pages: int
     *      }
     * }
     */
    public function index(ListSubscriptionsRequest $request): JsonResponse
    {
        $command = ListSubscriptionsCommand::fromArray($request->validated());

        $result = $this->handler->handle($command);

        return new JsonResponse(
            new SubscriptionCollectionResource($result->collection),
        );
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @response array{
     *     id: int,
     *     status: App\Domain\Shared\Enums\SubscriptionStatusEnum,
     *     user_id: int,
     *     expired_at: string,
     *     started_at: string,
     *     stripe_subscription_id: string
     * }
     */
    public function show(int $id): JsonResponse
    {
        $command = new GetSubscriptionCommand($id);

        $result = $this->getHandler->handle($command);

        return new JsonResponse(
            new SubscriptionResource($result->subscription),
        );
    }
}
