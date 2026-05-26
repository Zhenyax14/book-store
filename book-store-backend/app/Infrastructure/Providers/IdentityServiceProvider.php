<?php

namespace App\Infrastructure\Providers;

use App\Application\Identity\Interfaces\PasswordHasherInterface;
use App\Domain\Identity\Interfaces\AuthenticationServiceInterface;
use App\Domain\Identity\Interfaces\UserRepositoryInterface;
use App\Infrastructure\Auth\LaravelPasswordHasher;
use App\Infrastructure\Auth\SanctumAuthenticationService;
use App\Infrastructure\Persistence\Repositories\EloquentUserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

final class IdentityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(PasswordHasherInterface::class, LaravelPasswordHasher::class);

        $this->app->bind(
            AuthenticationServiceInterface::class,
            function (): SanctumAuthenticationService {
                return new SanctumAuthenticationService(
                    request: $this->app->make(Request::class),
                );
            },
        );
    }
}
