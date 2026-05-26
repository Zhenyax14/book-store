<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases\UploadBookFile;

use App\Application\Catalog\Events\BookFileUploaded;
use App\Application\Catalog\Interfaces\BookFileStorageInterface;
use App\Application\Shared\Interfaces\EventDispatcherInterface;
use App\Domain\Catalog\Exceptions\BookNotFoundException;
use App\Domain\Catalog\Interfaces\BookRepositoryInterface;

final readonly class UploadBookFileHandler
{
    public function __construct(
        private BookRepositoryInterface  $books,
        private BookFileStorageInterface $storage,
        private EventDispatcherInterface $dispatcher,
    ) {}

    public function handle(UploadBookFileCommand $command): UploadBookFileResult
    {
        $book = $this->books->findById($command->bookId);

        if ( ! $book) {
            throw new BookNotFoundException($command->bookId);
        }

        $path = $this->storage->upload(
            bookId: $command->bookId,
            tempPath: $command->tempPath,
            filename: $command->filename,
        );

        $this->dispatcher->dispatch(
            new BookFileUploaded(
                bookId: $command->bookId,
                filePath: $path,
                mimeType: $command->mimeType,
            ),
        );

        return new UploadBookFileResult(
            bookId: $book->id,
            filePath: $path,
        );
    }
}
