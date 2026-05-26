<?php

use App\Presentation\Http\Controllers\AdminAuthController;
use App\Presentation\Http\Controllers\AdminOrderController;
use App\Presentation\Http\Controllers\BookController;
use App\Presentation\Http\Controllers\BookCoverController;
use App\Presentation\Http\Controllers\BookFileController;
use App\Presentation\Http\Controllers\ReadingBookChapterController;
use App\Presentation\Http\Controllers\ReadingBookController;
use App\Presentation\Http\Controllers\ReadingBookPageController;
use App\Presentation\Http\Controllers\BookSearchController;
use App\Presentation\Http\Controllers\BookTagController;
use App\Presentation\Http\Controllers\CartController;
use App\Presentation\Http\Controllers\DashboardChartReadingSessionController;
use App\Presentation\Http\Controllers\DashboardStatisticController;
use App\Presentation\Http\Controllers\NotificationController;
use App\Presentation\Http\Controllers\PopularBooksController;
use App\Presentation\Http\Controllers\PublishBookController;
use App\Presentation\Http\Controllers\ReaderAuthController;
use App\Presentation\Http\Controllers\ReaderController;
use App\Presentation\Http\Controllers\ReadingHistoryController;
use App\Presentation\Http\Controllers\ReadingListController;
use App\Presentation\Http\Controllers\ReadingProgressController;
use App\Presentation\Http\Controllers\ReadingSessionController;
use App\Presentation\Http\Controllers\ReadingSettingsController;
use App\Presentation\Http\Controllers\StripeWebhookController;
use App\Presentation\Http\Controllers\SubscriptionCheckoutController;
use App\Presentation\Http\Controllers\SubscriptionController;
use App\Presentation\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('books/search', BookSearchController::class)->name('books.search');
    Route::get('/books/popular', PopularBooksController::class)->name('books.popular');
    Route::get('books', [BookController::class, 'index'])->name('books.index');
    Route::get('books/{id}', [BookController::class, 'show'])->name('books.show');
    Route::get('tags', [TagController::class, 'index'])->name('tags.index');

    Route::post('auth/register', [ReaderAuthController::class, 'register'])->name('auth.register');
    Route::post('auth/login', [ReaderAuthController::class, 'login'])->name('auth.login');
    Route::post('admin/auth/login', [AdminAuthController::class, 'login'])->name('admin.auth.login');

    Route::middleware(['auth:sanctum', 'role:reader'])
        ->group(static function (): void {
            Route::prefix('books/{bookId}')
                ->middleware('book.access')
                ->group(static function (): void {
                    Route::get('/read', ReadingBookController::class)
                        ->name('reading.book.show');

                    Route::get('progress', [ReadingProgressController::class, 'show'])
                        ->name('reading.progress.show');

                    Route::post('progress', [ReadingProgressController::class, 'save'])
                        ->name('reading.progress.save');

                    Route::get('chapters/{chapterId}', ReadingBookChapterController::class)
                        ->name('reading.chapter');

                    Route::get('pages/{pageId}', ReadingBookPageController::class)
                        ->name('reading.page');

                    Route::post('sessions', [ReadingSessionController::class, 'start'])
                        ->name('reading.session.start');
                    Route::patch('sessions/{sessionId}', [ReadingSessionController::class, 'end'])
                        ->name('reading.session.end');
                });

            Route::get('reading/history', ReadingHistoryController::class)
                ->name('reading.history');

            Route::get('reading/settings', [ReadingSettingsController::class, 'show'])
                ->name('reading.settings.show');
            Route::put('reading/settings', [ReadingSettingsController::class, 'update'])
                ->name('reading.settings.update');

            Route::post('auth/logout', [ReaderAuthController::class, 'logout'])->name('auth.logout');

            Route::prefix('reading-list')
                ->name('reading-list.')
                ->group(static function (): void {
                    Route::get('/', [ReadingListController::class, 'index'])->name('index');
                    Route::middleware('book.access')
                        ->group(static function (): void {
                            Route::post('/{bookId}', [ReadingListController::class, 'store'])->name('store');
                            Route::patch('/{bookId}/start', [ReadingListController::class, 'start'])->name('start');
                            Route::patch('/{bookId}/progress', [ReadingListController::class, 'progress'])->name('progress');
                            Route::delete('/{bookId}', [ReadingListController::class, 'destroy'])->name('destroy');
                        });

                });

            Route::prefix('notifications')->name('notifications.')->group(static function (): void {
                Route::get('/', [NotificationController::class, 'index'])->name('index');
                Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
                Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
                Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
            });

            Route::prefix('cart')->name('cart.')->group(static function (): void {
                Route::get('/', [CartController::class, 'show'])
                    ->name('show');
                Route::post('/items', [CartController::class, 'addItem'])
                    ->name('items.add');
                Route::delete('/items/{type}/{referenceId}', [CartController::class, 'removeItem'])
                    ->name('items.remove');
                Route::post('/checkout', [CartController::class, 'checkout'])
                    ->name('checkout');
            });

            Route::prefix('subscriptions')->name('subscriptions.')->group(static function (): void {
                Route::post('/checkout', SubscriptionCheckoutController::class)
                    ->name('checkout');
            });
        });

    Route::middleware(['auth:sanctum', 'role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(static function (): void {
            Route::post('books', [BookController::class, 'store'])->name('books.store');
            Route::put('books/{id}', [BookController::class, 'update'])->name('books.update');
            Route::patch('books/{id}/publish', PublishBookController::class)->name('books.publish');
            Route::delete('books/{id}', [BookController::class, 'destroy'])->name('books.destroy');

            Route::post('books/{id}/cover', BookCoverController::class)->name('books.cover');
            Route::post('books/{id}/book-file', BookFileController::class)->name('books.file');

            Route::post('books/{id}/tags', [BookTagController::class, 'sync'])->name('books.tags.sync');
            Route::post('books/{id}/tags/{tagId}', [BookTagController::class, 'attach'])->name('books.tags.attach');
            Route::delete('books/{id}/tags/{tagId}', [BookTagController::class, 'detach'])->name('books.tags.detach');

            Route::post('auth/logout', [AdminAuthController::class, 'logout'])->name('auth.logout');

            Route::get('dashboard/stats', DashboardStatisticController::class)
                ->name('dashboard.stats');

            Route::get('dashboard/charts/reading-sessions', DashboardChartReadingSessionController::class)
                ->name('dashboard.charts.reading-sessions');

            Route::get('readers', [ReaderController::class, 'index'])
                ->name('readers.index');
            Route::get('readers/{id}', [ReaderController::class, 'show'])
                ->name('readers.show');

            Route::get('orders', [AdminOrderController::class, 'index'])
                ->name('orders.index');
            Route::get('orders/{id}', [AdminOrderController::class, 'show'])
                ->name('orders.show');

            Route::get('subscriptions', [SubscriptionController::class, 'index'])
                ->name('subscriptions.index');
            Route::get('subscriptions/{id}', [SubscriptionController::class, 'show'])
                ->name('subscriptions.show');
        });

    Route::post('webhooks/stripe', StripeWebhookController::class)
        ->name('webhooks.stripe');
});
