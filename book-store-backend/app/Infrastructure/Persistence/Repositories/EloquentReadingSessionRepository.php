<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Reading\Entities\ReadingSession;
use App\Domain\Reading\Interfaces\ReadingSessionRepositoryInterface;
use App\Infrastructure\Persistence\Models\ReadingSessionModel;

final class EloquentReadingSessionRepository implements ReadingSessionRepositoryInterface
{
    public function save(ReadingSession $session): ReadingSession
    {
        $data = $this->toArray($session);

        if (null === $session->id) {
            $model = ReadingSessionModel::query()->create($data);
        } else {
            $model = ReadingSessionModel::query()->findOrFail($session->id);
            $model->update($data);
        }

        return $this->toDomain($model);
    }

    public function findById(int $id): ?ReadingSession
    {
        $model = ReadingSessionModel::query()->find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findActiveByUser(int $userId, int $bookId): ?ReadingSession
    {
        $model = ReadingSessionModel::query()
            ->forUser($userId)
            ->forBook($bookId)
            ->active()
            ->latest('started_at')
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    /** @return ReadingSession[] */
    public function findByUser(int $userId): array
    {
        return ReadingSessionModel::query()
            ->forUser($userId)
            ->recentFirst()
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    private function toDomain(ReadingSessionModel $model): ReadingSession
    {
        return new ReadingSession(
            id: $model->id,
            userId: $model->user_id,
            bookId: $model->book_id,
            startPageId: $model->start_page_id,
            endPageId: $model->end_page_id,
            startedAt: $model->started_at,
            endedAt: $model->ended_at,
            pagesRead: $model->pages_read,
            durationSeconds: $model->duration_seconds,
        );
    }

    private function toArray(ReadingSession $session): array
    {
        return [
            'user_id'          => $session->userId,
            'book_id'          => $session->bookId,
            'start_page_id'    => $session->startPageId,
            'end_page_id'      => $session->endPageId,
            'started_at'       => $session->startedAt,
            'ended_at'         => $session->endedAt,
            'pages_read'       => $session->pagesRead,
            'duration_seconds' => $session->durationSeconds,
        ];
    }
}
