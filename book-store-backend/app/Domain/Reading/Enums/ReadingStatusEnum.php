<?php

declare(strict_types=1);

namespace App\Domain\Reading\Enums;

enum ReadingStatusEnum: string
{
    case WANT_TO_READ = 'want_to_read';
    case READING    = 'reading';
    case FINISHED   = 'finished';
    case DROPPED    = 'dropped';

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::WANT_TO_READ => in_array($next, [self::READING, self::DROPPED], true),
            self::READING    => in_array($next, [self::FINISHED, self::DROPPED], true),
            self::FINISHED   => false,
            self::DROPPED    => self::READING === $next,
        };
    }
}
