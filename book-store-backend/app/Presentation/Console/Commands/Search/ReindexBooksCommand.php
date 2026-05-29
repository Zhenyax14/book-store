<?php

declare(strict_types=1);

namespace App\Presentation\Console\Commands\Search;

use App\Domain\Catalog\Interfaces\BookRepositoryInterface;
use App\Infrastructure\Search\MeilisearchIndexConfigurator;
use Illuminate\Console\Command;

final class ReindexBooksCommand extends Command
{
    protected $signature   = 'search:reindex';

    protected $description = 'Reindex all books in MeiliSearch via zero-downtime swap';

    public function __construct(
        private readonly BookRepositoryInterface      $books,
        private readonly MeilisearchIndexConfigurator $configurator,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Starting reindex...');

        $this->configurator->reindexWithSwap(
            $this->books->cursor(),
        );

        $this->info('Done.');

        return self::SUCCESS;
    }
}
