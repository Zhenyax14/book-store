<?php

namespace App\Application\Catalog\Interfaces;

interface BookFileStorageInterface
{
    public function upload(int $bookId, string $tempPath, string $filename): string;

    public function url(string $path): string;

    public function delete(string $path): void;

    public function exists(string $path): bool;

    /** @return string[] */
    public function listFiles(string $directory): array;

    public function deleteDirectory(string $directory): void;
}
