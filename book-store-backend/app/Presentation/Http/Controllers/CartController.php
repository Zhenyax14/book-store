<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Cart\UseCases\AddItem\AddItemToCartHandler;
use App\Application\Cart\UseCases\Checkout\CheckoutHandler;
use App\Application\Cart\UseCases\GetCart\GetCartCommand;
use App\Application\Cart\UseCases\GetCart\GetCartHandler;
use App\Application\Cart\UseCases\RemoveItem\RemoveItemFromCartHandler;
use App\Presentation\Http\Requests\Cart\AddItemToCartRequest;
use App\Presentation\Http\Requests\Cart\CheckoutRequest;
use App\Presentation\Http\Resources\Cart\CartResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Cart\Enums\CartItemTypeEnum;
use App\Application\Cart\UseCases\AddItem\AddItemToCartCommand;
use App\Application\Cart\UseCases\Checkout\CheckoutCommand;
use App\Application\Cart\UseCases\RemoveItem\RemoveItemFromCartCommand;

final class CartController extends Controller
{
    public function __construct(
        private readonly GetCartHandler            $getHandler,
        private readonly AddItemToCartHandler      $addHandler,
        private readonly RemoveItemFromCartHandler $removeHandler,
        private readonly CheckoutHandler           $checkoutHandler,
    ) {}

    /**
     * @response array{
     *     id: string|null,
     *     status: \App\Domain\Cart\Enums\CartStatusEnum,
     *     items: array<int, array{
     *         type: \App\Domain\Cart\Enums\CartItemTypeEnum,
     *         reference_id: int,
     *         title: string,
     *         price: array{
     *             amount: int,
     *             currency: string,
     *             formatted: string
     *         }
     *     }>,
     *     total: array{
     *         amount: int,
     *         currency: string,
     *         formatted: string
     *     },
     *     items_count: int,
     *     created_at: string
     * }
     */
    public function show(Request $request): JsonResponse
    {
        $cart = $this->getHandler->handle(
            new GetCartCommand($request->user()->id),
        );

        if (null === $cart) {
            return new JsonResponse(['data' => null]);
        }

        return new JsonResponse(
            new CartResource($cart)->withCurrency('EUR'),
        );
    }

    public function addItem(AddItemToCartRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $cart = $this->addHandler->handle(
            new AddItemToCartCommand(
                userId: $request->user()->id,
                type: CartItemTypeEnum::from($validated['type']),
                referenceId: (int) $validated['reference_id'],
            ),
        );

        return new JsonResponse(
            new CartResource($cart)->withCurrency('EUR'),
            Response::HTTP_OK,
        );
    }

    public function removeItem(Request $request, string $type, int $referenceId): JsonResponse
    {
        $cart = $this->removeHandler->handle(
            new RemoveItemFromCartCommand(
                userId: $request->user()->id,
                type: CartItemTypeEnum::from($type),
                referenceId: $referenceId,
            ),
        );

        return new JsonResponse(
            new CartResource($cart)->withCurrency('EUR'),
        );
    }

    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $result = $this->checkoutHandler->handle(
            new CheckoutCommand(
                userId: $request->user()->id,
                currency: $request->validated('currency'),
            ),
        );

        return new JsonResponse([
            'cart_id' => $result->cartId,
            'total' => $result->total->toArray(),
            'payment_url' => $result->paymentUrl,
        ]);
    }
}
