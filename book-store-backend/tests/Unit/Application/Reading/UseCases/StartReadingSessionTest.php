<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reading\UseCases;

use App\Application\Reading\UseCases\StartReadingSession\StartReadingSessionCommand;
use App\Application\Reading\UseCases\StartReadingSession\StartReadingSessionHandler;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeReadingSessionRepository;

final class StartReadingSessionTest extends TestCase
{
    private FakeReadingSessionRepository $sessions;

    private StartReadingSessionHandler   $handler;

    protected function setUp(): void
    {
        $this->sessions = new FakeReadingSessionRepository();
        $this->handler  = new StartReadingSessionHandler($this->sessions);
    }

    public function test_creates_new_session(): void
    {
        $result = $this->handler->handle(
            new StartReadingSessionCommand(userId: 1, bookId: 10, currentPageId: 5),
        );

        $this->assertFalse($result->isResumed);
        $this->sessions->assertSessionStarted(1, 10);
    }

    public function test_resumes_existing_active_session(): void
    {
        $first = $this->handler->handle(
            new StartReadingSessionCommand(userId: 1, bookId: 10, currentPageId: 5),
        );

        $second = $this->handler->handle(
            new StartReadingSessionCommand(userId: 1, bookId: 10, currentPageId: 12),
        );

        $this->assertTrue($second->isResumed);
        $this->assertEquals($first->sessionId, $second->sessionId);

        $this->sessions->assertCount(1);
    }

    public function test_creates_separate_sessions_for_different_books(): void
    {
        $this->handler->handle(new StartReadingSessionCommand(userId: 1, bookId: 10, currentPageId: null));
        $this->handler->handle(new StartReadingSessionCommand(userId: 1, bookId: 20, currentPageId: null));

        $this->sessions->assertCount(2);
    }
}
