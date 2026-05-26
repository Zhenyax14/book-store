<?php

use App\Infrastructure\Providers\AppServiceProvider;
use App\Infrastructure\Providers\IdentityServiceProvider;
use App\Infrastructure\Providers\NotificationServiceProvider;
use App\Infrastructure\Providers\ScrambleServiceProvider;

return [
    AppServiceProvider::class,
    IdentityServiceProvider::class,
    NotificationServiceProvider::class,
    ScrambleServiceProvider::class,
];
