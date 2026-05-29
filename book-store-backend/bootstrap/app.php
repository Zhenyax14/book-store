<?php

use App\Domain\Access\Exceptions\SubscriptionAlreadyActiveException;
use App\Domain\Cart\Exceptions\CartAlreadyCheckedOutException;
use App\Domain\Cart\Exceptions\CartItemAlreadyExistsException;
use App\Domain\Cart\Exceptions\CartItemNotFoundException;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Catalog\Exceptions\BookNotFoundException;
use App\Domain\Catalog\Exceptions\TagNotFoundException;
use App\Domain\Identity\Exceptions\InvalidCredentialsException;
use App\Domain\Identity\Exceptions\ReaderAlreadyExistsException;
use App\Domain\Notification\Exceptions\NotificationNotFoundException;
use App\Domain\Reading\Exceptions\InvalidReadingStatusTransitionException;
use App\Domain\Reading\Exceptions\ReadingEntryAlreadyExistsException;
use App\Domain\Reading\Exceptions\ReadingEntryNotFoundException;
use App\Presentation\Console\Commands\Search\ConfigureSearchIndexCommand;
use App\Presentation\Console\Commands\Search\ReindexBooksCommand;
use App\Presentation\Http\Middleware\RequireBookAccessMiddleware;
use App\Presentation\Http\Middleware\RequireRoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RequireRoleMiddleware::class,
            'book.access' => RequireBookAccessMiddleware::class,
        ]);
    })
    ->withCommands([
        ConfigureSearchIndexCommand::class,
        ReindexBooksCommand::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReportDuplicates();
        $exceptions->render(function (Throwable $e) {
            return match (true) {
                $e instanceof CartNotFoundException,
                $e instanceof CartItemNotFoundException,
                $e instanceof BookNotFoundException,
                $e instanceof NotificationNotFoundException,
                $e instanceof ReadingEntryNotFoundException,
                $e instanceof TagNotFoundException => response()->json([
                    'message' => $e->getMessage(),
                ], Response::HTTP_NOT_FOUND),
                $e instanceof CartItemAlreadyExistsException,
                $e instanceof ReaderAlreadyExistsException,
                $e instanceof SubscriptionAlreadyActiveException,
                $e instanceof ReadingEntryAlreadyExistsException => response()->json(
                    ['message' => $e->getMessage()],
                    Response::HTTP_CONFLICT,
                ),
                $e instanceof InvalidCredentialsException => response()->json(
                    ['message' => $e->getMessage()],
                    Response::HTTP_UNAUTHORIZED,
                ),
                $e instanceof InvalidReadingStatusTransitionException,
                $e instanceof CartAlreadyCheckedOutException => response()->json(
                    ['message' => $e->getMessage()],
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                ),
                default => null,
            };
        });
    })->create();
