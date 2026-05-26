<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Reading\Entities\UserReadingProgress;
use App\Domain\Reading\Interfaces\UserReadingProgressRepositoryInterface;
use App\Domain\Reading\ValueObjects\ReadingPosition;
use App\Infrastructure\Persistence\Models\UserReadingProgressModel;

final class EloquentUserReadingProgressRepository implements UserReadingProgressRepositoryInterface
{
    public function findByUserAndBook(int $userId, int $bookId): ?UserReadingProgress
    {
        $model = UserReadingProgressModel::query()
            ->forUser($userId)
            ->forBook($bookId)
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function save(UserReadingProgress $progress): UserReadingProgress
    {
        $data = $this->toArray($progress);

        if (null === $progress->id) {
            $model = UserReadingProgressModel::query()->create($data);
        } else {
            $model = UserReadingProgressModel::query()->findOrFail($progress->id);
            $model->update($data);
        }

        return $this->toDomain($model);
    }

    /** @return UserReadingProgress[] */
    public function findAllByUser(int $userId): array
    {
        return UserReadingProgressModel::forUser($userId)
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    private function toDomain(UserReadingProgressModel $model): UserReadingProgress
    {
        $position = (null !== $model->page_id && null !== $model->chapter_id)
            ? new ReadingPosition(
                bookId: $model->book_id,
                chapterId: $model->chapter_id,
                pageId: $model->page_id,
                globalPageNumber: $model->global_page_number ?? 0,
                scrollPosition: $model->scroll_position,
            )
            : null;

        return new UserReadingProgress(
            id: $model->id,
            userId: $model->user_id,
            bookId: $model->book_id,
            totalPages: $model->book?->pages_count ?? 0,
            position: $position,
            completionPercentage: $model->completion_percentage,
            isFinished: $model->is_finished,
            lastReadAt: $model->last_read_at,
            finishedAt: $model->finished_at,
        );
    }

    private function toArray(UserReadingProgress $progress): array
    {
        return [
            'user_id'               => $progress->userId,
            'book_id'               => $progress->bookId,
            'chapter_id'            => $progress->position?->chapterId,
            'page_id'               => $progress->position?->pageId,
            'scroll_position'       => $progress->position?->scrollPosition ?? 0,
            'completion_percentage' => $progress->completionPercentage,
            'is_finished'           => $progress->isFinished,
            'last_read_at'          => $progress->lastReadAt,
            'finished_at'           => $progress->finishedAt,
        ];
    }
}
