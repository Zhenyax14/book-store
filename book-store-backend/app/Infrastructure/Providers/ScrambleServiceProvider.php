<?php

declare(strict_types=1);

namespace App\Infrastructure\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\Components;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Dedoc\Scramble\Support\Generator\Server;
use Dedoc\Scramble\Support\Generator\Tag;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Registers and configures the Scramble OpenAPI documentation generator.
 *
 * Responsibilities:
 *  - Set API info (title, description, version, contact, license)
 *  - Register security schemes (Bearer token via Sanctum)
 *  - Define server entries
 *  - Filter routes to v1 API prefix only
 *  - Define OpenAPI tag groups for logical grouping in the UI
 */
final class ScrambleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Scramble::routes(static function (Route $route): bool {
            return str_starts_with($route->uri(), 'api/v1/');
        });

        Scramble::afterOpenApiGenerated(static function (OpenApi $openApi): void {
            $openApi->info->title       = 'Book Store API';
            $openApi->info->version     = config('app.version', '1.0.0');
            $openApi->info->description = self::description();

            $openApi->components ??= new Components();
            $openApi->components->securitySchemes['bearerAuth'] = SecurityScheme::http(
                scheme: 'bearer',
                bearerFormat: 'token',
            );

            $openApi->servers = [
                Server::make(
                    url: mb_rtrim(config('app.url'), '/') . '/api',
                )->setDescription('Current environment'),
            ];

            $openApi->tags = self::tags();
        });
    }

    private static function description(): string
    {
        return <<<'MD'
## Book Store API

A full-featured digital bookstore backend built with Laravel, following Clean Architecture
and Domain-Driven Design principles.

### Authentication

Most endpoints require a **Bearer token** issued by Sanctum after login.

- Reader endpoints: `POST /auth/login` or `POST /auth/register`
- Admin endpoints: `POST /admin/auth/login`

Include the token in every protected request:
```
Authorization: Bearer <token>
```

### Access Control

| Role   | Scope                                                              |
|--------|--------------------------------------------------------------------|
| Guest  | Browse books, search, view tags                                    |
| Reader | Reading list, progress tracking, sessions, cart, notifications     |
| Admin  | Book management (create/update/delete/publish), file & cover upload |

### Pagination

Paginated responses follow a consistent envelope:
```json
{
  "data": [...],
  "meta": {
    "total": 100,
    "per_page": 20,
    "current_page": 1,
    "total_pages": 5
  }
}
```

### Money

Prices are stored and returned as **integer cents** (e.g. `1990` = €19.90).
A `formatted` field is always included for display convenience.

### Error Codes

| HTTP | Meaning                                    |
|------|--------------------------------------------|
| 401  | Unauthenticated                            |
| 403  | Forbidden (wrong role or no book access)   |
| 404  | Resource not found                         |
| 409  | Conflict (duplicate entry)                 |
| 422  | Validation error                           |
MD;
    }

    /** @return array<int, Tag> */
    private static function tags(): array
    {
        $definitions = [
            'Books'         => 'Book catalog — browsing, searching, publishing, cover & file management.',
            'Tags'          => 'Taxonomy tags for categorising books.',
            'Reader Auth'   => 'Registration, login and logout for readers.',
            'Admin Auth'    => 'Login and logout for administrators.',
            'Reading List'  => 'Per-user reading list: want_to_read → reading → finished / dropped.',
            'Progress'      => 'Granular page-level reading progress with Redis-cached scroll positions.',
            'Sessions'      => 'Reading sessions tracking time spent and pages read per sitting.',
            'History'       => 'Aggregated reading history across all sessions.',
            'Pages'         => 'Book page content with adjacent-page navigation and inline progress.',
            'Notifications' => 'In-app notifications (book published, purchase receipt, reading finished).',
            'Cart'          => 'Shopping cart supporting books and subscription plan items.',
            'Subscriptions' => 'Stripe-backed subscription checkout session initiation.',
            'Webhooks'      => 'Stripe webhook receiver — grants book access and activates subscriptions.',
        ];

        return array_map(static function (string $name, string $description): Tag {
            $tag              = new Tag($name);
            $tag->description = $description;

            return $tag;
        }, array_keys($definitions), array_values($definitions));
    }
}
