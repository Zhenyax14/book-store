<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reading\UseCases;

use App\Application\Reading\UseCases\EndReadingSession\EndReadingSessionCommand;
use App\Application\Reading\UseCases\EndReadingSession\EndReadingSessionHandler;
use App\Application\Reading\UseCases\StartReadingSession\StartReadingSessionCommand;
use App\Application\Reading\UseCases\StartReadingSession\StartReadingSessionHandler;
use App\Domain\Reading\Exceptions\SessionNotFoundException;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeReadingSessionRepository;

final class EndReadingSessionTest extends TestCase
{
    private FakeReadingSessionRepository $sessions;

    private EndReadingSessionHandler     $handler;

    protected function setUp(): void
    {
        $this->sessions = new FakeReadingSessionRepository();
        $this->handler  = new EndReadingSessionHandler($this->sessions);
    }

    public function test_ends_session_and_records_stats(): void
    {
        $startHandler = new StartReadingSessionHandler($this->sessions);
        $started = $startHandler->handle(
            new StartReadingSessionCommand(userId: 1, bookId: 10, currentPageId: 5),
        );

        $result = $this->handler->handle(new EndReadingSessionCommand(
            sessionId: $started->sessionId,
            endPageId: 25,
            durationSeconds: 1200,
        ));

        $this->assertEquals(1200, $result->durationSeconds);
        $this->assertEquals(20, $result->pagesRead);   // 25 - 5
        $this->sessions->assertSessionEnded($started->sessionId);
    }

    public function test_throws_when_session_not_found(): void
    {
        $this->expectException(SessionNotFoundException::class);

        $this->handler->handle(new EndReadingSessionCommand(
            sessionId: 9999,
            endPageId: 10,
            durationSeconds: 60,
        ));
    }
}
