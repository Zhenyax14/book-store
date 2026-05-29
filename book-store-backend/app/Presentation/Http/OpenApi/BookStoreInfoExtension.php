<?php

namespace App\Presentation\Http\OpenApi;

use Dedoc\Scramble\Extensions\OperationExtension;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\RouteInfo;
use Illuminate\Support\Str;

final class BookStoreInfoExtension extends OperationExtension
{
    private const array TAG_MAP = [
        // Catalog
        'BookController'         => ['Books'],
        'BookSearchController'   => ['Books'],
        'PopularBooksController' => ['Books'],
        'BookCoverController'    => ['Books'],
        'BookFileController'     => ['Books'],
        'BookTagController'      => ['Tags'],
        'TagController'          => ['Tags'],
        'PublishBookController'  => ['Books'],

        // Identity
        'ReaderAuthController' => ['Reader Auth'],
        'AdminAuthController'  => ['Admin Auth'],

        // Reading
        'ReadingListController'     => ['Reading List'],
        'ReadingProgressController' => ['Progress'],
        'ReadingSessionController'  => ['Sessions'],
        'ReadingHistoryController'  => ['History'],
        'BookPageController'        => ['Pages'],

        // Notifications
        'NotificationController' => ['Notifications'],

        // Commerce
        'CartController'                 => ['Cart'],
        'SubscriptionCheckoutController' => ['Subscriptions'],

        // Payments
        'StripeWebhookController' => ['Webhooks'],
    ];

    public function handle(Operation $operation, RouteInfo $routeInfo): void
    {
        $controllerClass = class_basename($routeInfo->route->getControllerClass() ?? '');

        $tags            = self::TAG_MAP[$controllerClass] ?? [$this->inferTag($controllerClass)];
        $operation->tags = array_unique(array_merge($operation->tags ?? [], $tags));
    }

    private function inferTag(string $controllerClass): string
    {
        return Str::of($controllerClass)
            ->replace('Controller', '')
            ->headline()
            ->toString();
    }
}
