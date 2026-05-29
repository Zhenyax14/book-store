<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Reading\Entities\ReadingEntry;
use App\Domain\Reading\Enums\ReadingStatusEnum;
use App\Domain\Reading\Interfaces\ReadingListRepositoryInterface;
use App\Domain\Reading\ValueObjects\ReadingEntryCollection;
use App\Infrastructure\Persistence\Models\ReadingEntryModel;

final class EloquentReadingListRepository implements ReadingListRepositoryInterface
{
    public function findByUser(int $userId, ?ReadingStatusEnum $status, int $perPage, int $page): ReadingEntryCollection
    {
        $paginator = ReadingEntryModel::query()
            ->with('book')
            ->where('user_id', $userId)
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('updated_at')
            ->paginate(perPage: $perPage, page: $page);

        return new ReadingEntryCollection(
            items: array_map(
                fn(ReadingEntryModel $model) => $this->toDomain($model),
                $paginator->items(),
            ),
            total: $paginator->total(),
            perPage: $paginator->perPage(),
            currentPage: $paginator->currentPage(),
        );
    }

    public function findEntry(int $userId, int $bookId): ?ReadingEntry
    {
        $model = ReadingEntryModel::query()
            ->where('user_id', $userId)
            ->where('book_id', $bookId)
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function save(ReadingEntry $entry): ReadingEntry
    {
        $data = [
            'user_id'     => $entry->userId,
            'book_id'     => $entry->bookId,
            'status'      => $entry->status->value,
            'current_page' => $entry->currentPage,
            'total_pages' => $entry->totalPages,
            'started_at'  => $entry->startedAt,
            'finished_at' => $entry->finishedAt,
        ];

        if (null === $entry->id) {
            $model = ReadingEntryModel::query()->create($data);
        } else {
            $model = ReadingEntryModel::query()->findOrFail($entry->id);
            $model->update($data);
        }

        return $this->toDomain($model);
    }

    public function delete(int $userId, int $bookId): void
    {
        ReadingEntryModel::query()
            ->where('user_id', $userId)
            ->where('book_id', $bookId)
            ->firstOrFail()
            ->delete();
    }

    public function existsForUser(int $userId, int $bookId): bool
    {
        return ReadingEntryModel::query()
            ->where('user_id', $userId)
            ->where('book_id', $bookId)
            ->exists();
    }

    private function toDomain(ReadingEntryModel $model): ReadingEntry
    {
        return new ReadingEntry(
            userId: $model->user_id,
            bookId: $model->book_id,
            status: $model->status,
            currentPage: $model->current_page,
            totalPages: $model->total_pages,
            startedAt: $model->started_at,
            finishedAt: $model->finished_at,
            id: $model->id,
        );
    }
}
