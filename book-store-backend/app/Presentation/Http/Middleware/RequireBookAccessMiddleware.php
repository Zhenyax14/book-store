<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use App\Domain\Access\Interfaces\BookAccessCheckerInterface;
use App\Infrastructure\Persistence\Models\UserModel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verifies that the authenticated user has read access to the requested book.
 *
 * Usage in routes:
 *   Route::middleware(['auth:sanctum', 'book.access'])->group(...)
 *
 * The route must contain a {bookId} parameter.
 */
final readonly class RequireBookAccessMiddleware
{
    public function __construct(
        private BookAccessCheckerInterface $checker,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        /** @var UserModel|null $user */
        $user = $request->user();

        if (null === $user) {
            return response()->json(
                ['message' => 'Unauthenticated.'],
                Response::HTTP_UNAUTHORIZED,
            );
        }

        $bookId = (int) $request->route('bookId');

        if (0 === $bookId) {
            return response()->json(
                ['message' => 'Book identifier is missing.'],
                Response::HTTP_BAD_REQUEST,
            );
        }

        if ( ! $this->checker->canRead($user->id, $bookId)) {
            return response()->json(
                ['message' => 'You do not have access to this book.'],
                Response::HTTP_FORBIDDEN,
            );
        }

        return $next($request);
    }
}
