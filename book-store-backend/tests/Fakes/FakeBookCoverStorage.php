<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Application\Catalog\Interfaces\BookCoverStorageInterface;

final class FakeBookCoverStorage implements BookCoverStorageInterface
{
    private array $uploaded = [];

    private array $deleted  = [];

    public function upload(int $bookId, string $tempPath, string $filename): string
    {
        $path = "covers/{$bookId}/{$filename}";
        $this->uploaded[$path] = $tempPath;

        return $path;
    }

    public function url(string $path): string
    {
        return "https://fake-storage.test/{$path}";
    }

    public function delete(string $path): void
    {
        $this->deleted[] = $path;
        unset($this->uploaded[$path]);
    }

    public function wasUploaded(string $path): bool
    {
        return isset($this->uploaded[$path]);
    }

    public function wasDeleted(string $path): bool
    {
        return in_array($path, $this->deleted, true);
    }

    public function getDeleted(): array
    {
        return $this->deleted;
    }
}
