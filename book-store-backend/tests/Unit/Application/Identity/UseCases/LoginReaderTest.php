<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Identity\UseCases;

use App\Application\Identity\UseCases\LoginReader\LoginReaderCommand;
use App\Application\Identity\UseCases\LoginReader\LoginReaderHandler;
use App\Application\Identity\UseCases\RegisterReader\RegisterReaderCommand;
use App\Application\Identity\UseCases\RegisterReader\RegisterReaderHandler;
use App\Domain\Identity\Entities\User;
use App\Domain\Identity\Exceptions\InvalidCredentialsException;
use App\Domain\Identity\ValueObjects\Email;
use App\Domain\Identity\ValueObjects\HashedPassword;
use App\Domain\Shared\Enums\RoleEnum;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeAuthenticationService;
use Tests\Fakes\FakeEventDispatcher;
use Tests\Fakes\FakePasswordHasher;
use Tests\Fakes\FakeUserRepository;

final class LoginReaderTest extends TestCase
{
    private FakeUserRepository        $users;

    private FakePasswordHasher        $hasher;

    private LoginReaderHandler        $handler;

    protected function setUp(): void
    {
        $this->users   = new FakeUserRepository();
        $auth = new FakeAuthenticationService();
        $this->hasher  = new FakePasswordHasher();
        $this->handler = new LoginReaderHandler($this->users, $auth, $this->hasher);
        $eventDispatcher = new FakeEventDispatcher();
        new RegisterReaderHandler($this->users, $auth, $this->hasher, $eventDispatcher)
            ->handle(new RegisterReaderCommand('John', 'john@example.com', 'secret123'));
    }

    public function test_returns_token_on_valid_credentials(): void
    {
        $result = $this->handler->handle(
            new LoginReaderCommand('john@example.com', 'secret123'),
        );

        $this->assertStringStartsWith('fake-token-', $result->token->value);
    }

    public function test_throws_on_wrong_password(): void
    {
        $this->expectException(InvalidCredentialsException::class);

        $this->handler->handle(
            new LoginReaderCommand('john@example.com', 'wrong'),
        );
    }

    public function test_throws_on_unknown_email(): void
    {
        $this->expectException(InvalidCredentialsException::class);

        $this->handler->handle(
            new LoginReaderCommand('unknown@example.com', 'secret123'),
        );
    }

    public function test_throws_when_user_is_admin(): void
    {
        // Создаём admin напрямую через репозиторий
        $admin = User::register(
            'Admin',
            new Email('admin@example.com'),
            new HashedPassword($this->hasher->hash('adminpass')),
        );
        $adminWithRole = new \ReflectionClass($admin);
        $admin = new User(
            id: null,
            name: 'Admin',
            email: new Email('admin@example.com'),
            password: new HashedPassword($this->hasher->hash('adminpass')),
            role: RoleEnum::ADMIN,
        );
        $this->users->save($admin);

        $this->expectException(InvalidCredentialsException::class);

        $this->handler->handle(
            new LoginReaderCommand('admin@example.com', 'adminpass'),
        );
    }
}
