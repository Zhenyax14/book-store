<?php

namespace App\Infrastructure\Providers;

use App\Application\Access\Services\BookAccessChecker;
use App\Application\Cart\Interfaces\PaymentGatewayInterface;
use App\Application\Cart\Services\CartItemPriceResolver;
use App\Application\Catalog\Interfaces\BookCoverStorageInterface;
use App\Application\Catalog\Interfaces\BookFileParserInterface;
use App\Application\Catalog\Interfaces\BookFileStorageInterface;
use App\Application\Catalog\Interfaces\BookSearchIndexInterface;
use App\Application\Shared\Interfaces\EventDispatcherInterface;
use App\Application\Shared\Interfaces\SlugGeneratorInterface;
use App\Domain\Access\Interfaces\BookAccessCheckerInterface;
use App\Domain\Access\Interfaces\UserBookAccessRepositoryInterface;
use App\Domain\Access\Interfaces\UserSubscriptionAccessRepositoryInterface;
use App\Domain\Cart\Interfaces\CartItemPriceResolverInterface;
use App\Domain\Cart\Interfaces\CartRepositoryInterface;
use App\Domain\Catalog\Interfaces\BookPopularityRepositoryInterface;
use App\Domain\Catalog\Interfaces\BookRepositoryInterface;
use App\Domain\Reading\Interfaces\BookRepositoryInterface as ReadingBookRepositoryInterface;
use App\Domain\Catalog\Interfaces\BookTagRepositoryInterface;
use App\Domain\Catalog\Interfaces\TagRepositoryInterface;
use App\Domain\Dashboard\Interfaces\DashboardRepositoryInterface;
use App\Domain\Order\Interfaces\OrderRepositoryInterface;
use App\Domain\Reading\Interfaces\BookChapterRepositoryInterface;
use App\Domain\Reading\Interfaces\BookPageRepositoryInterface;
use App\Domain\Reading\Interfaces\ReadingListRepositoryInterface;
use App\Domain\Reading\Interfaces\ReadingProgressCacheRepositoryInterface;
use App\Domain\Reading\Interfaces\ReadingSessionRepositoryInterface;
use App\Domain\Reading\Interfaces\UserReadingProgressRepositoryInterface;
use App\Domain\Reading\Interfaces\UserReadingSettingsRepositoryInterface;
use App\Domain\Subscription\Interfaces\UserSubscriptionRepositoryInterface;
use App\Domain\User\Interfaces\ReaderRepositoryInterface;
use App\Infrastructure\Cache\RedisReadingProgressCacheRepository;
use App\Infrastructure\Parser\BookFileParserRouter;
use App\Infrastructure\Payment\StripePaymentGateway;
use App\Infrastructure\Persistence\Repositories\EloquentBookChapterRepository;
use App\Infrastructure\Persistence\Repositories\EloquentBookPageRepository;
use App\Infrastructure\Persistence\Repositories\EloquentBookPopularityRepository;
use App\Infrastructure\Persistence\Repositories\EloquentBookRepository;
use App\Infrastructure\Persistence\Repositories\EloquentBookTagRepository;
use App\Infrastructure\Persistence\Repositories\EloquentCartRepository;
use App\Infrastructure\Persistence\Repositories\EloquentDashboardRepository;
use App\Infrastructure\Persistence\Repositories\EloquentOrderRepository;
use App\Infrastructure\Persistence\Repositories\EloquentReaderRepository;
use App\Infrastructure\Persistence\Repositories\EloquentReadingListRepository;
use App\Infrastructure\Persistence\Repositories\EloquentReadingSessionRepository;
use App\Infrastructure\Persistence\Repositories\EloquentTagRepository;
use App\Infrastructure\Persistence\Repositories\EloquentUserBookAccessRepository;
use App\Infrastructure\Persistence\Repositories\EloquentUserReadingProgressRepository;
use App\Infrastructure\Persistence\Repositories\EloquentUserReadingSettingsRepository;
use App\Infrastructure\Persistence\Repositories\EloquentUserSubscriptionRepository;
use App\Infrastructure\Queue\LaravelEventDispatcher;
use App\Infrastructure\Search\MeilisearchBookIndex;
use App\Infrastructure\Search\MeilisearchIndexConfigurator;
use App\Infrastructure\Search\MeilisearchTaskAwaiter;
use App\Infrastructure\Slugger\LaravelSlugGenerator;
use App\Infrastructure\Storage\S3BookCoverStorage;
use App\Infrastructure\Storage\S3BookFileStorage;
use Illuminate\Support\ServiceProvider;
use Meilisearch\Client;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, function () {
            return new Client(
                url: config('services.meilisearch.host'),
                apiKey: config('services.meilisearch.key'),
            );
        });

        $this->app->singleton(
            MeilisearchTaskAwaiter::class,
            fn() => new MeilisearchTaskAwaiter(
                client: app(Client::class),
                timeoutMs: (int) config('services.meilisearch.timeout_ms', 5000),
                intervalMs: (int) config('services.meilisearch.interval_ms', 50),
            ),
        );
        $this->app->singleton(MeilisearchIndexConfigurator::class);

        $this->app->bind(
            UserReadingProgressRepositoryInterface::class,
            EloquentUserReadingProgressRepository::class,
        );

        $this->app->bind(
            ReadingSessionRepositoryInterface::class,
            EloquentReadingSessionRepository::class,
        );

        $this->app->bind(
            ReadingProgressCacheRepositoryInterface::class,
            RedisReadingProgressCacheRepository::class,
        );

        $this->app->singleton(StripeClient::class, fn() => new StripeClient(
            config('services.stripe.secret'),
        ));

        $this->app->bind(PaymentGatewayInterface::class, fn($app) => new StripePaymentGateway(
            stripe: $app->make(StripeClient::class),
            webhookSecret: config('services.stripe.webhook_secret'),
            successUrl: config('app.frontend_url') . '/payment/success',
            cancelUrl:  config('app.frontend_url') . '/payment/decline',
        ));

        $this->app->bind(BookRepositoryInterface::class, EloquentBookRepository::class);
        $this->app->bind(BookFileParserInterface::class, BookFileParserRouter::class);
        $this->app->bind(SlugGeneratorInterface::class, LaravelSlugGenerator::class);
        $this->app->bind(EventDispatcherInterface::class, LaravelEventDispatcher::class);
        $this->app->bind(BookSearchIndexInterface::class, MeilisearchBookIndex::class);
        $this->app->bind(BookCoverStorageInterface::class, S3BookCoverStorage::class);
        $this->app->bind(BookFileStorageInterface::class, S3BookFileStorage::class);
        $this->app->bind(BookChapterRepositoryInterface::class, EloquentBookChapterRepository::class);
        $this->app->bind(BookPageRepositoryInterface::class, EloquentBookPageRepository::class);
        $this->app->bind(TagRepositoryInterface::class, EloquentTagRepository::class);
        $this->app->bind(BookTagRepositoryInterface::class, EloquentBookTagRepository::class);
        $this->app->bind(BookPopularityRepositoryInterface::class, EloquentBookPopularityRepository::class);
        $this->app->bind(ReadingListRepositoryInterface::class, EloquentReadingListRepository::class);
        $this->app->bind(CartRepositoryInterface::class, EloquentCartRepository::class);
        $this->app->bind(CartItemPriceResolverInterface::class, CartItemPriceResolver::class);
        $this->app->bind(BookAccessCheckerInterface::class, BookAccessChecker::class);
        $this->app->bind(UserBookAccessRepositoryInterface::class, EloquentUserBookAccessRepository::class);
        $this->app->bind(UserSubscriptionAccessRepositoryInterface::class, EloquentUserSubscriptionRepository::class);
        $this->app->bind(DashboardRepositoryInterface::class, EloquentDashboardRepository::class);
        $this->app->bind(ReaderRepositoryInterface::class, EloquentReaderRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, EloquentOrderRepository::class);
        $this->app->bind(UserSubscriptionRepositoryInterface::class, EloquentUserSubscriptionRepository::class);
        $this->app->bind(UserReadingSettingsRepositoryInterface::class, EloquentUserReadingSettingsRepository::class);
        $this->app->bind(ReadingBookRepositoryInterface::class, EloquentBookRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
