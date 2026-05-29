<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use App\Domain\Reading\Interfaces\ReadingProgressCacheRepositoryInterface;
use App\Domain\Reading\ValueObjects\ReadingPosition;
use JsonException;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;

final class RedisReadingProgressCacheRepository implements ReadingProgressCacheRepositoryInterface
{
    // Позиция скролла — горячие данные, живут 7 дней без активности
    private const int TTL_SECONDS = 60 * 60 * 24 * 7;

    private const string KEY_PREFIX = 'reading_progress';

    /**
     * @throws JsonException
     */
    public function get(int $userId, int $bookId): ?ReadingPosition
    {
        $raw = $this->connection()->get($this->key($userId, $bookId));

        if (null === $raw || false === $raw) {
            return null;
        }

        $data = json_decode((string) $raw, associative: true, flags: JSON_THROW_ON_ERROR);

        return ReadingPosition::fromArray($data);
    }

    /**
     * @throws JsonException
     */
    public function set(int $userId, ReadingPosition $position): void
    {
        $this->connection()->setex(
            $this->key($userId, $position->bookId),
            self::TTL_SECONDS,
            json_encode($position->toArray(), JSON_THROW_ON_ERROR),
        );
    }

    public function forget(int $userId, int $bookId): void
    {
        $this->connection()->del($this->key($userId, $bookId));
    }

    private function key(int $userId, int $bookId): string
    {
        return sprintf('%s:user:%d:book:%d', self::KEY_PREFIX, $userId, $bookId);
    }

    private function connection(): Connection
    {
        return Redis::connection('cache');
    }
}
