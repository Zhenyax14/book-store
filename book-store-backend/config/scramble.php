<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

return [

    /*
    |--------------------------------------------------------------------------
    | API Path
    |--------------------------------------------------------------------------
    |
    | The path where the API documentation will be available.
    |
    */
    'api_path' => 'api',

    /*
    |--------------------------------------------------------------------------
    | API Domain
    |--------------------------------------------------------------------------
    |
    | The domain where the API is hosted. Leave null to use the app domain.
    |
    */
    'api_domain' => null,

    /*
    |--------------------------------------------------------------------------
    | OpenAPI Info
    |--------------------------------------------------------------------------
    |
    | The info object for the OpenAPI specification.
    |
    */
    'info' => [
        'version' => env('APP_VERSION', '1.0.0'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Documentation Path
    |--------------------------------------------------------------------------
    |
    | The path where the Swagger/Redoc UI will be served.
    |
    */
    'docs_path' => 'docs/api',

    /*
    |--------------------------------------------------------------------------
    | Documentation Route Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware applied to the documentation routes.
    | Use RestrictedDocsAccess to limit access in production.
    |
    */
    'middleware' => [
        'web',
        RestrictedDocsAccess::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Server URLs
    |--------------------------------------------------------------------------
    |
    | Define server URLs for the OpenAPI spec.
    | Scramble auto-detects the current app URL if left empty.
    |
    */
    'servers' => null,

    /*
    |--------------------------------------------------------------------------
    | Extensions
    |--------------------------------------------------------------------------
    |
    | Custom OpenAPI extensions to enrich the generated spec.
    |
    */
    'extensions' => [
        App\Presentation\Http\OpenApi\BookStoreInfoExtension::class,
    ],

];
