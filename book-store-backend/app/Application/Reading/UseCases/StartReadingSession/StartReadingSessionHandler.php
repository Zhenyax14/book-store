<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\StartReadingSession;

use App\Domain\Reading\Entities\ReadingSession;
use App\Domain\Reading\Interfaces\ReadingSessionRepositoryInterface;

final readonly class StartReadingSessionHandler
{
    public function __construct(
        private ReadingSessionRepositoryInterface $sessions,
    ) {}

    public function handle(StartReadingSessionCommand $command): StartReadingSessionResult
    {
        // Идемпотентность: не дублируем активную сессию
        $active = $this->sessions->findActiveByUser($command->userId, $command->bookId);
        if (null !== $active) {
            return new StartReadingSessionResult($active->id, isResumed: true);
        }

        $session = $this->sessions->save(
            ReadingSession::begin($command->userId, $command->bookId, $command->currentPageId),
        );

        return new StartReadingSessionResult($session->id, isResumed: false);
    }
}
