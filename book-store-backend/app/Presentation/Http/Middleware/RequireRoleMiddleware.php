<?php

namespace App\Presentation\Http\Middleware;

use App\Domain\Shared\Enums\RoleEnum;
use App\Infrastructure\Persistence\Models\UserModel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RequireRoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        /** @var UserModel|null $user */
        $user = $request->user();

        if (null === $user) {
            return response()->json(
                ['message' => 'Unauthenticated.'],
                Response::HTTP_UNAUTHORIZED,
            );
        }

        if ($user->role !== RoleEnum::from($role)) {
            return response()->json(
                ['message' => 'Forbidden.'],
                Response::HTTP_FORBIDDEN,
            );
        }

        return $next($request);
    }
}
