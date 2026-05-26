<?php

namespace App\Presentation\Http\Resources\Catalog;

use App\Application\Catalog\DTOs\BookFileLink;
use App\Application\Catalog\Interfaces\BookCoverStorageInterface;
use App\Domain\Catalog\Entities\Book;
use App\Domain\Catalog\Enums\AccessTypeEnum;
use App\Domain\Catalog\Enums\BookStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Book $resource
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property string $isbn
 * @property string $language
 * @property string $publisher
 * @property int $published_year
 * @property int $pages_count
 * @property string $cover_path
 * @property AccessTypeEnum $access_type
 * @property BookStatusEnum $status
 * @property int $price
 * @property string $currency
 * @property bool $is_free
 * @property string $file_path
 * @property string $published_at
 */
final class BookResource extends JsonResource
{
    private BookCoverStorageInterface $storage;

    private array $fileLinks = [];

    public function withStorage(BookCoverStorageInterface $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    public function withFileLinks(array $fileLinks): self
    {
        $this->fileLinks = $fileLinks;

        return $this;
    }

    public function toArray(Request $request): array
    {
        $book = $this->resource;

        return [
            'id'             => $book->id,
            'title'          => $book->title,
            'slug'           => $book->slug,
            'description'    => $book->description,
            'isbn'           => $book->isbn,
            'language'       => $book->language,
            'publisher'      => $book->publisher,
            'published_year' => $book->publishedYear,
            'pages_count'    => $book->pagesCount,
            'cover_url'      => $book->coverPath
                ? $this->storage->url($book->coverPath)
                : null,
            'file_links'     => array_map(
                static fn(BookFileLink $link) => [
                    'mime_type' => $link->mimeType,
                    'url'       => $link->url,
                    'label'     => $link->label,
                ],
                $this->fileLinks,
            ),
            'access_type'    => $book->accessType->value,
            'price'          => [
                'currency'  => $book->price->currency->code,
                'amount'    => $book->price->amount,
                'formatted' => $book->price->format(),
            ],
            'status'         => $book->status->value,
            'is_free'        => $book->isFree(),
            'published_at'   => $book->publishedAt?->format('Y-m-d'),
        ];
    }
}
