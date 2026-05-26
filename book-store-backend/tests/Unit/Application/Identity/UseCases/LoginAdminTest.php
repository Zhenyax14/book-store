<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Identity\UseCases;

use App\Application\Identity\UseCases\LoginAdmin\LoginAdminCommand;
use App\Application\Identity\UseCases\LoginAdmin\LoginAdminHandler;
use App\Domain\Identity\Entities\User;
use App\Domain\Identity\Exceptions\InvalidCredentialsException;
use App\Domain\Identity\ValueObjects\Email;
use App\Domain\Identity\ValueObjects\HashedPassword;
use App\Domain\Shared\Enums\RoleEnum;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeAuthenticationService;
use Tests\Fakes\FakePasswordHasher;
use Tests\Fakes\FakeUserRepository;

final class LoginAdminTest extends TestCase
{
    private FakeUserRepository        $users;

    private FakePasswordHasher        $hasher;

    private LoginAdminHandler         $handler;

    protected function setUp(): void
    {
        $this->users   = new FakeUserRepository();
        $auth = new FakeAuthenticationService();
        $this->hasher  = new FakePasswordHasher();
        $this->handler = new LoginAdminHandler($this->users, $auth, $this->hasher);

        $this->users->save(new User(
            id: null,
            name: 'Admin',
            email: new Email('admin@example.com'),
            password: new HashedPassword($this->hasher->hash('adminpass')),
            role: RoleEnum::ADMIN,
        ));
    }

    public function test_returns_token_on_valid_credentials(): void
    {
        $result = $this->handler->handle(
            new LoginAdminCommand('admin@example.com', 'adminpass'),
        );

        $this->assertStringStartsWith('fake-token-', $result->token->value);
        $this->assertSame(RoleEnum::ADMIN, $result->user->getRole());
    }

    public function test_throws_on_wrong_password(): void
    {
        $this->expectException(InvalidCredentialsException::class);
        $this->handler->handle(new LoginAdminCommand('admin@example.com', 'wrong'));
    }

    public function test_throws_when_user_is_reader(): void
    {
        $this->users->save(new User(
            id: null,
            name: 'Reader',
            email: new Email('reader@example.com'),
            password: new HashedPassword($this->hasher->hash('pass')),
            role: RoleEnum::READER,
        ));

        $this->expectException(InvalidCredentialsException::class);
        $this->handler->handle(new LoginAdminCommand('reader@example.com', 'pass'));
    }
}
