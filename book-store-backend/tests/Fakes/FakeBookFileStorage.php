<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Application\Catalog\Interfaces\BookFileStorageInterface;

final class FakeBookFileStorage implements BookFileStorageInterface
{
    private array $uploaded = [];

    private array $deleted  = [];

    private array $existing = [];

    public function upload(int $bookId, string $tempPath, string $filename): string
    {
        $path = "books/{$bookId}/{$filename}";
        $this->uploaded[$path] = $tempPath;

        return $path;
    }

    public function exists(string $path): bool
    {
        return isset($this->uploaded[$path]) || isset($this->existing[$path]);
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

    public function listFiles(string $directory): array
    {
        $all = array_merge(
            array_keys($this->uploaded),
            array_keys($this->existing),
        );

        return array_values(array_filter(
            $all,
            static fn(string $path) => str_starts_with($path, $directory),
        ));
    }

    public function registerExisting(string $path): void
    {
        $this->existing[$path] = true;
    }

    public function deleteDirectory(string $directory): void
    {
        $this->uploaded = array_filter(
            $this->uploaded,
            static fn(string $path) => ! str_starts_with($path, $directory),
            ARRAY_FILTER_USE_KEY,
        );

        $this->existing = array_filter(
            $this->existing,
            static fn(string $path) => ! str_starts_with($path, $directory),
            ARRAY_FILTER_USE_KEY,
        );

        $this->deleted[] = $directory;
    }
}
