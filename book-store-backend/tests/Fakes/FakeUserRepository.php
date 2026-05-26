<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Domain\Identity\Entities\User;
use App\Domain\Identity\Interfaces\UserRepositoryInterface;
use App\Domain\Identity\ValueObjects\Email;
use App\Domain\Identity\ValueObjects\UserId;
use PHPUnit\Framework\Assert;

final class FakeUserRepository implements UserRepositoryInterface
{
    /** @var User[] */
    private array $users = [];

    private int   $nextId = 1;

    public function save(User $user): User
    {
        $saved = new User(
            id: new UserId($user->getId()?->value ?? $this->nextId++),
            name: $user->getName(),
            email: $user->getEmail(),
            password: $user->getPassword(),
            role: $user->getRole(),
        );

        $this->users[$saved->getId()->value] = $saved;

        return $saved;
    }

    public function findByEmail(Email $email): ?User
    {
        return array_find($this->users, fn($user) => $user->getEmail()->equals($email));
    }

    public function existsByEmail(Email $email): bool
    {
        return null !== $this->findByEmail($email);
    }

    public function assertUserSaved(string $email): void
    {
        $found = $this->findByEmail(new Email($email));
        Assert::assertNotNull(
            $found,
            "Expected user with email '{$email}' to be saved.",
        );
    }

    public function assertCount(int $expected): void
    {
        Assert::assertCount($expected, $this->users);
    }
}
