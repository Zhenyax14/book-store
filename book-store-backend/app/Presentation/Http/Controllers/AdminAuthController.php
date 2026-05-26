<?php

namespace App\Presentation\Http\Controllers;

use App\Application\Identity\UseCases\LoginAdmin\LoginAdminCommand;
use App\Application\Identity\UseCases\LoginAdmin\LoginAdminHandler;
use App\Application\Identity\UseCases\Logout\LogoutCommand;
use App\Application\Identity\UseCases\Logout\LogoutHandler;
use App\Domain\Identity\ValueObjects\UserId;
use App\Presentation\Http\Requests\Identity\LoginRequest;
use App\Presentation\Http\Resources\Identity\AuthResultResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminAuthController extends Controller
{
    public function __construct(
        private readonly LoginAdminHandler $loginHandler,
        private readonly LogoutHandler         $logoutHandler,
    ) {}

    /**
     * @response array{
     *     token: string,
     *     user: array{
     *         id: int,
     *         name: string,
     *         email: string,
     *         role: \App\Domain\Identity\Enums\RoleEnum
     *     }
     * }
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->loginHandler->handle(
            LoginAdminCommand::fromArray($request->validated()),
        );

        return new JsonResponse(
            new AuthResultResource($result),
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->logoutHandler->handle(
            new LogoutCommand(
                new UserId($request->user()->id),
            ),
        );

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
