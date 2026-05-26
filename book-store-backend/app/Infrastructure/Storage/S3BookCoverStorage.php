<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Application\Catalog\Interfaces\BookCoverStorageInterface;
use Illuminate\Support\Facades\Storage;

final class S3BookCoverStorage implements BookCoverStorageInterface
{
    public function upload(int $bookId, string $tempPath, string $filename): string
    {
        $path = "covers/{$bookId}/{$filename}";
        Storage::disk('s3')->putFileAs("covers/{$bookId}", $tempPath, $filename);

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
}
