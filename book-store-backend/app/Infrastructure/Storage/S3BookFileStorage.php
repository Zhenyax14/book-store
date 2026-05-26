<?php

namespace App\Infrastructure\Storage;

use App\Application\Catalog\Interfaces\BookFileStorageInterface;
use Illuminate\Support\Facades\Storage;

class S3BookFileStorage implements BookFileStorageInterface
{
    public function upload(int $bookId, string $tempPath, string $filename): string
    {
        $path = "books/{$bookId}/{$filename}";
        Storage::disk('s3')->putFileAs("books/{$bookId}", $tempPath, $filename);

        return $path;
    }

    public function url(string $path): string
    {
        return Storage::disk('s3')->url($path);
    }

    public function delete(string $path): void
    {
        Storage::disk('s3')->delete($path);
    }

    public function deleteDirectory(string $directory): void
    {
        Storage::disk('s3')->deleteDirectory($directory);
    }

    public function exists(string $path): bool
    {
        return Storage::disk('s3')->exists($path);
    }

    public function listFiles(string $directory): array
    {
        return Storage::disk('s3')->files($directory);
    }
}
