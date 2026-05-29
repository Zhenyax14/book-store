<?php

declare(strict_types=1);

namespace App\Infrastructure\Search;

use Meilisearch\Client;
use Meilisearch\Exceptions\ApiException;
use Meilisearch\Exceptions\TimeOutException;
use RuntimeException;

final readonly class MeilisearchTaskAwaiter
{
    // Значения по умолчанию покрывают 99% случаев.
    // Для reindex крупных каталогов передавай больший timeout через DI.
    public function __construct(
        private Client $client,
        private int    $timeoutMs,   // макс. время ожидания одной задачи
        private int    $intervalMs,  // как часто поллим статус
    ) {}

    /**
     * Ждёт завершения одной задачи.
     * Бросает исключение если задача упала или истёк таймаут.
     */
    public function wait(int $taskUid): void
    {
        try {
            $task = $this->client->waitForTask(
                uid: $taskUid,
                timeoutInMs: $this->timeoutMs,
                intervalInMs: $this->intervalMs,
            );
        } catch (TimeOutException) {
            throw new RuntimeException(
                "MeiliSearch task #{$taskUid} did not complete within {$this->timeoutMs}ms. "
                . 'Consider increasing timeout or checking MeiliSearch health.',
            );
        } catch (ApiException $e) {
            throw new RuntimeException(
                "MeiliSearch API error while waiting for task #{$taskUid}: {$e->getMessage()}",
                previous: $e,
            );
        }

        // waitForTask может вернуть задачу в статусе 'failed' без исключения —
        // это надо проверять отдельно.
        $this->assertTaskSucceeded($task, $taskUid);
    }

    /**
     * Ждёт завершения нескольких задач последовательно.
     * Подходит для батчевых операций.
     *
     * @param int[] $taskUids
     */
    public function waitAll(array $taskUids): void
    {
        foreach ($taskUids as $uid) {
            $this->wait($uid);
        }
    }

    /**
     * Ждёт завершения нескольких задач с общим дедлайном.
     * Полезно когда важно общее время, а не время каждой задачи.
     *
     * @param int[] $taskUids
     */
    public function waitAllWithDeadline(array $taskUids, int $totalTimeoutMs): void
    {
        $deadline = microtime(true) * 1000 + $totalTimeoutMs;

        foreach ($taskUids as $uid) {
            $remaining = (int) ($deadline - microtime(true) * 1000);

            if ($remaining <= 0) {
                throw new RuntimeException(
                    'Deadline exceeded while waiting for MeiliSearch tasks. '
                    . 'Remaining tasks: ' . implode(', ', array_slice($taskUids, array_search($uid, $taskUids))),
                );
            }

            $awaiter = new self($this->client, $remaining, $this->intervalMs);
            $awaiter->wait($uid);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // Private
    // ──────────────────────────────────────────────────────────────

    private function assertTaskSucceeded(array $task, int $taskUid): void
    {
        if ('succeeded' !== $task['status']) {
            $errorMessage = $task['error']['message'] ?? 'unknown error';
            $errorCode = $task['error']['code'] ?? 'unknown';
            $errorType = $task['error']['type'] ?? 'unknown';

            throw new RuntimeException(
                "MeiliSearch task #{$taskUid} failed. "
                . "Status: {$task['status']}. "
                . "Error [{$errorCode}/{$errorType}]: {$errorMessage}",
            );
        }
    }
}
