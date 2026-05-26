<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Domain\Identity\Entities\User;
use App\Domain\Identity\Interfaces\AuthenticationServiceInterface;
use App\Domain\Identity\ValueObjects\AuthToken;
use App\Domain\Identity\ValueObjects\UserId;
use PHPUnit\Framework\Assert;

final class FakeAuthenticationService implements AuthenticationServiceInterface
{
    private array $issuedTokens  = [];

    private array $revokedTokens = [];

    public function issueToken(User $user): AuthToken
    {
        $token = new AuthToken('fake-token-' . $user->getId()->value);
        $this->issuedTokens[$user->getId()->value] = $token;

        return $token;
    }

    public function revokeCurrentToken(UserId $userId): void
    {
        $this->revokedTokens[] = $userId->value;
    }

    public function assertTokenIssuedFor(int $userId): void
    {
        Assert::assertArrayHasKey(
            $userId,
            $this->issuedTokens,
            "Expected token to be issued for user {$userId}.",
        );
    }

    public function assertTokenRevokedFor(int $userId): void
    {
        Assert::assertContains(
            $userId,
            $this->revokedTokens,
            "Expected token to be revoked for user {$userId}.",
        );
    }
}
