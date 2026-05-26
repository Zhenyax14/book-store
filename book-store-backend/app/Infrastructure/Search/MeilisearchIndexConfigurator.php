<?php

declare(strict_types=1);

namespace App\Infrastructure\Search;

use App\Domain\Catalog\Entities\Book;
use Meilisearch\Client;

final readonly class MeilisearchIndexConfigurator
{
    private const string INDEX_NAME   = 'books';

    private const string INDEX_BUFFER = 'books_buffer'; // для zero-downtime swap

    public function __construct(
        private readonly Client                $client,
        private readonly MeilisearchTaskAwaiter $awaiter,
    ) {}

    // ──────────────────────────────────────────────────────────────
    // Index settings
    // ──────────────────────────────────────────────────────────────

    /**
     * Применяет все настройки к production-индексу.
     * Вызывается: php artisan search:configure
     * Когда вызывать: при деплое, если изменились поля индекса.
     *
     * ВАЖНО: изменение searchableAttributes или filterableAttributes
     * запускает полное переиндексирование на стороне MeiliSearch.
     * Это может занять минуты на больших каталогах.
     */
    public function configure(): void
    {
        $this->applySettings(self::INDEX_NAME);
    }

    /**
     * Возвращает текущие настройки индекса.
     * Полезно для диагностики в Artisan-командах.
     */
    public function getSettings(): array
    {
        return $this->client->index(self::INDEX_NAME)->getSettings();
    }

    // ──────────────────────────────────────────────────────────────
    // Zero-downtime reindex
    // ──────────────────────────────────────────────────────────────

    /**
     * Переиндексирует все книги без даунтайма через index swap.
     *
     * Алгоритм:
     *   1. Заполняем буферный индекс (books_buffer) новыми данными
     *   2. Атомарно меняем местами books ↔ books_buffer
     *   3. Очищаем books_buffer (теперь там старые данные)
     *
     * В момент свапа поисковые запросы к 'books' продолжают работать —
     * они просто мгновенно переключаются на новые данные.
     *
     * @param iterable<Book> $books
     */
    public function reindexWithSwap(iterable $books): void
    {
        // Шаг 1: убеждаемся что буферный индекс существует и пустой
        $this->resetBuffer();

        // Шаг 2: применяем настройки к буферу — он должен быть идентичен production
        $this->applySettings(self::INDEX_BUFFER);

        // Шаг 3: заливаем данные батчами, ждём завершения каждого батча
        $this->fillBuffer($books);

        // Шаг 4: атомарный своп
        $this->swapWithProduction();

        // Шаг 5: очищаем буфер (теперь там старые production-данные)
        $this->clearBuffer();
    }

    // ──────────────────────────────────────────────────────────────
    // Private: settings
    // ──────────────────────────────────────────────────────────────

    private function applySettings(string $indexName): void
    {
        $index = $this->client->index($indexName);

        // Searchable attributes — поля, по которым идёт полнотекстовый поиск.
        // Порядок определяет вес: title важнее description, description важнее isbn.
        // Если поле не указано здесь — оно не участвует в поиске.
        $task = $index->updateSearchableAttributes([
            'title',
            'description',
            'isbn',
        ]);
        $this->awaiter->wait($task['taskUid']);

        // Filterable attributes — поля для filter= параметра.
        // БЕЗ этой настройки любой filter= вернёт ошибку invalid_search_filter.
        // После добавления нового поля — MeiliSearch перестраивает inverted index.
        $task = $index->updateFilterableAttributes([
            'status',
            'access_type',
            'language',
        ]);
        $this->awaiter->wait($task['taskUid']);

        // Sortable attributes — поля для sort= параметра.
        // Хранятся в отдельной структуре, расходуют дополнительную память.
        // Добавляй только то, что реально нужно для сортировки в UI.
        $task = $index->updateSortableAttributes([
            'title',
        ]);
        $this->awaiter->wait($task['taskUid']);

        // Ranking rules — приоритет факторов ранжирования.
        // Это дефолтный набор MeiliSearch — явно прописываем для наглядности.
        //
        // words      — документы с большим числом совпадений слов выше
        // typo       — меньше опечаток = выше
        // proximity  — слова запроса стоят ближе друг к другу = выше
        // attribute  — совпадение в searchableAttributes[0] важнее чем в [1]
        // sort       — применяется если передан sort= параметр
        // exactness  — точное совпадение > опечатка
        $task = $index->updateRankingRules([
            'words',
            'typo',
            'proximity',
            'attribute',
            'sort',
            'exactness',
        ]);
        $this->awaiter->wait($task['taskUid']);

        // Typo tolerance — насколько терпимы к опечаткам.
        // oneTypo: минимальная длина слова для допуска 1 опечатки (default: 5)
        // twoTypos: минимальная длина слова для допуска 2 опечаток (default: 9)
        // Для книжного магазина снижаем порог — "Dostoveski" должен найти "Dostoevsky"
        $task = $index->updateTypoTolerance([
            'enabled'             => true,
            'minWordSizeForTypos' => [
                'oneTypo'  => 4,
                'twoTypos' => 8,
            ],
        ]);
        $this->awaiter->wait($task['taskUid']);

        // Pagination — максимальное число хитов, доступных через offset/limit.
        // Default: 1000. Увеличивай осторожно — большие значения дорого обходятся.
        $task = $index->updatePagination(['maxTotalHits' => 1000]);
        $this->awaiter->wait($task['taskUid']);
    }

    // ──────────────────────────────────────────────────────────────
    // Private: swap pipeline
    // ──────────────────────────────────────────────────────────────

    private function resetBuffer(): void
    {
        try {
            // Создаём индекс если не существует (primaryKey обязателен при создании)
            $this->client->createIndex(self::INDEX_BUFFER, ['primaryKey' => 'id']);
        } catch (\Throwable) {
            // Индекс уже существует — просто чистим его
        }

        $task = $this->client->index(self::INDEX_BUFFER)->deleteAllDocuments();
        $this->awaiter->wait($task['taskUid']);
    }

    /**
     * @param iterable<Book> $books
     */
    private function fillBuffer(iterable $books): void
    {
        $batch    = [];
        $taskUids = [];

        foreach ($books as $book) {
            $batch[] = $this->toDocument($book);

            if (500 === count($batch)) {
                $task     = $this->client->index(self::INDEX_BUFFER)->addDocuments($batch);
                $taskUids[] = $task['taskUid'];
                $batch    = [];
            }
        }

        // Последний неполный батч
        if ( ! empty($batch)) {
            $task     = $this->client->index(self::INDEX_BUFFER)->addDocuments($batch);
            $taskUids[] = $task['taskUid'];
        }

        if (empty($taskUids)) {
            return; // Нет данных для индексации
        }

        // Ждём завершения всех батчей перед свапом
        $this->awaiter->waitAll($taskUids);
    }

    private function swapWithProduction(): void
    {
        // swapIndexes — атомарная операция.
        // После неё 'books' содержит новые данные, 'books_buffer' — старые.
        // Формат: массив пар для свапа (можно свапать несколько пар за раз).
        $swapTask = $this->client->swapIndexes([
            ['indexes' => [self::INDEX_NAME, self::INDEX_BUFFER]],
        ]);

        $this->awaiter->wait($swapTask['taskUid']);
    }

    private function clearBuffer(): void
    {
        // После свапа в буфере лежат старые production-данные — удаляем.
        // Не удаляем сам индекс — он понадобится при следующем reindex.
        $task = $this->client->index(self::INDEX_BUFFER)->deleteAllDocuments();
        $this->awaiter->wait($task['taskUid']);
    }

    private function toDocument(mixed $book): array
    {
        return [
            'id'          => $book->id,
            'title'       => $book->title,
            'slug'        => $book->slug,
            'description' => $book->description ?? '',
            'isbn'        => $book->isbn ?? '',
            'language'    => $book->language,
            'access_type' => $book->accessType->value,
            'status'      => $book->status->value,
        ];
    }
}
