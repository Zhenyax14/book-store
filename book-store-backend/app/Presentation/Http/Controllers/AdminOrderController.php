<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Order\UseCases\GetOrder\GetOrderCommand;
use App\Application\Order\UseCases\GetOrder\GetOrderHandler;
use App\Application\Order\UseCases\ListOrders\ListOrdersCommand;
use App\Application\Order\UseCases\ListOrders\ListOrdersHandler;
use App\Presentation\Http\Requests\Order\ListOrdersRequest;
use App\Presentation\Http\Resources\Order\OrderCollectionResource;
use App\Presentation\Http\Resources\Order\OrderResource;
use Illuminate\Http\JsonResponse;

final class AdminOrderController extends Controller
{
    public function __construct(
        private readonly ListOrdersHandler $listOrdersHandler,
        private readonly GetOrderHandler   $getOrderHandler,
    ) {}

    /**
     * @param ListOrdersRequest $request
     * @return JsonResponse
     * @response array{
     *     data: array<int, array{
     *         id: string,
     *         total: int,
     *         items: array<int, array{
     *             type: App\Domain\Order\Enums\OrderItemTypeEnum,
     *             title: string,
     *             price: int,
     *             quantity: int,
     *             access_granted: bool,
     *         }>,
     *         user: array{
     *             id: int,
     *             name: string,
     *             email: string,
     *         },
     *         item_count: int,
     *         stripe_payment_intent: string,
     *         checked_out_at: string,
     *     }>,
     *     meta: array{
     *         total: int,
     *         per_page: int,
     *         current_page: int,
     *         total_pages: int}
     * }
     */
    public function index(ListOrdersRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->listOrdersHandler->handle(new ListOrdersCommand(
            perPage: (int) ($validated['per_page'] ?? 20),
            page: (int) ($validated['page'] ?? 1),
            search: $validated['search'] ?? null,
            dateFrom: $validated['date_from'] ?? null,
            dateTo: $validated['date_to'] ?? null,
        ));

        return new JsonResponse(new OrderCollectionResource($result->collection));
    }

    /**
     * @response array{
     *     id: string,
     *     total: int,
     *     items: array<int, array{
     *         type: App\Domain\Order\Enums\OrderItemTypeEnum,
     *         title: string,
     *         price: int,
     *         quantity: int,
     *         access_granted: bool,
     *     }>,
     *     user: array{
     *         id: int,
     *         name: string,
     *         email: string,
     *     },
     *     item_count: int,
     *     stripe_payment_intent: string,
     *     checked_out_at: string,
     * }
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->getOrderHandler->handle(
            new GetOrderCommand($id),
        );

        return new JsonResponse(new OrderResource($result->order));
    }
}
