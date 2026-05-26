<?php

namespace App\Presentation\Http\Controllers;

use App\Application\Identity\UseCases\LoginReader\LoginReaderCommand;
use App\Application\Identity\UseCases\LoginReader\LoginReaderHandler;
use App\Application\Identity\UseCases\Logout\LogoutCommand;
use App\Application\Identity\UseCases\Logout\LogoutHandler;
use App\Application\Identity\UseCases\RegisterReader\RegisterReaderCommand;
use App\Application\Identity\UseCases\RegisterReader\RegisterReaderHandler;
use App\Domain\Identity\ValueObjects\UserId;
use App\Presentation\Http\Requests\Identity\LoginRequest;
use App\Presentation\Http\Requests\Identity\RegisterReaderRequest;
use App\Presentation\Http\Resources\Identity\AuthResultResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

final class ReaderAuthController extends Controller
{
    public function __construct(
        private readonly RegisterReaderHandler $registerHandler,
        private readonly LoginReaderHandler    $loginHandler,
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
    public function register(RegisterReaderRequest $request): JsonResponse
    {
        $result = $this->registerHandler->handle(
            RegisterReaderCommand::fromArray($request->validated()),
        );

        return new JsonResponse(new AuthResultResource($result), Response::HTTP_CREATED);
    }

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
            LoginReaderCommand::fromArray($request->validated()),
        );

        return new JsonResponse(new AuthResultResource($result));
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
