<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Identity\UseCases;

use App\Application\Identity\UseCases\RegisterReader\RegisterReaderCommand;
use App\Application\Identity\UseCases\RegisterReader\RegisterReaderHandler;
use App\Domain\Identity\Exceptions\ReaderAlreadyExistsException;
use App\Domain\Identity\ValueObjects\Email;
use App\Domain\Shared\Enums\RoleEnum;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeAuthenticationService;
use Tests\Fakes\FakeEventDispatcher;
use Tests\Fakes\FakePasswordHasher;
use Tests\Fakes\FakeUserRepository;

final class RegisterReaderTest extends TestCase
{
    private FakeUserRepository       $users;

    private FakeAuthenticationService $auth;

    private RegisterReaderHandler     $handler;

    protected function setUp(): void
    {
        $this->users   = new FakeUserRepository();
        $this->auth    = new FakeAuthenticationService();
        $hasher = new FakePasswordHasher();
        $eventDispatcher = new FakeEventDispatcher();
        $this->handler = new RegisterReaderHandler($this->users, $this->auth, $hasher, $eventDispatcher);
    }

    public function test_saves_user_with_reader_role(): void
    {
        $this->handler->handle(new RegisterReaderCommand(
            name: 'John Doe',
            email: 'john@example.com',
            plainPassword: 'secret123',
        ));

        $this->users->assertUserSaved('john@example.com');

        $user = $this->users->findByEmail(new Email('john@example.com'));
        $this->assertSame(RoleEnum::READER, $user->getRole());
    }

    public function test_hashes_password(): void
    {
        $this->handler->handle(new RegisterReaderCommand(
            name: 'John Doe',
            email: 'john@example.com',
            plainPassword: 'secret123',
        ));

        $user = $this->users->findByEmail(new Email('john@example.com'));
        $this->assertSame('hashed:secret123', $user->getPassword()->value);
    }

    public function test_issues_token_after_registration(): void
    {
        $result = $this->handler->handle(new RegisterReaderCommand(
            name: 'John Doe',
            email: 'john@example.com',
            plainPassword: 'secret123',
        ));

        $this->auth->assertTokenIssuedFor($result->user->getId()->value);
        $this->assertStringStartsWith('fake-token-', $result->token->value);
    }

    public function test_throws_when_email_already_exists(): void
    {
        $command = new RegisterReaderCommand(
            name: 'John Doe',
            email: 'john@example.com',
            plainPassword: 'secret123',
        );

        $this->handler->handle($command);

        $this->expectException(ReaderAlreadyExistsException::class);
        $this->handler->handle($command);
    }

    public function test_does_not_save_user_when_email_already_exists(): void
    {
        $command = new RegisterReaderCommand(
            name: 'John Doe',
            email: 'john@example.com',
            plainPassword: 'secret123',
        );

        $this->handler->handle($command);

        try {
            $this->handler->handle($command);
        } catch (ReaderAlreadyExistsException) {
        }

        $this->users->assertCount(1);
    }
}
