<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Catalog\Interfaces\BookCoverStorageInterface;
use App\Application\Catalog\UseCases\GetTags\ListTagsCommand;
use App\Application\Catalog\UseCases\GetTags\ListTagsHandler;
use App\Application\Catalog\UseCases\ListBooks\ListBooksCommand;
use App\Application\Catalog\UseCases\ListBooks\ListBooksHandler;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Requests\Catalog\ListBooksRequest;
use App\Presentation\Http\Resources\Catalog\BookCollectionResource;
use Illuminate\Contracts\View\View;

final class CatalogController extends Controller
{
    public function __construct(
        private readonly ListBooksHandler         $handler,
        private readonly BookCoverStorageInterface $storage,
        private readonly ListTagsHandler          $listTagsHandler,
    ) {}

    public function __invoke(ListBooksRequest $request): View
    {
        $result = $this->handler->handle(
            ListBooksCommand::fromArray($request->validated()),
        );

        ['data' => $books, 'meta' => $meta] = (new BookCollectionResource($result->collection))
            ->withStorage($this->storage)
            ->resolve($request);

        $tagsResult = $this->listTagsHandler->handle(
            ListTagsCommand::fromArray([]),
        );



        return view('catalog.index', [
            'books'   => $books,
            'meta'    => $meta,
            'filters' => $request->only(['tag', 'access_type']),
            'tags'    => $tagsResult->collection->items,
        ]);
    }
}
