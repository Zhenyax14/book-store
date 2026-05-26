<?php

namespace App\Application\Reading\UseCases\EndReadingSession;

use App\Domain\Reading\Exceptions\SessionNotFoundException;
use App\Domain\Reading\Interfaces\ReadingSessionRepositoryInterface;

final readonly class EndReadingSessionHandler
{
    public function __construct(
        private ReadingSessionRepositoryInterface $sessions,
    ) {}

    public function handle(EndReadingSessionCommand $command): EndReadingSessionResult
    {
        $session = $this->sessions->findById($command->sessionId)
            ?? throw new SessionNotFoundException($command->sessionId);

        $closed = $session->end($command->endPageId, $command->durationSeconds);
        $this->sessions->save($closed);

        return new EndReadingSessionResult(
            pagesRead: $closed->pagesRead,
            durationSeconds: $closed->durationSeconds,
        );
    }
}
