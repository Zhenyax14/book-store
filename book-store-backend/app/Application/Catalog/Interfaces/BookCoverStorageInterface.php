<?php

namespace App\Application\Catalog\Interfaces;

interface BookCoverStorageInterface
{
    public function url(string $path): string;

    public function upload(int $bookId, string $tempPath, string $filename): string;

    public function delete(string $path): void;
}
