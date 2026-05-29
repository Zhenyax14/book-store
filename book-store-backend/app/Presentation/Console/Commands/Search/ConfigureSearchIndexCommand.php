<?php

declare(strict_types=1);

namespace App\Presentation\Console\Commands\Search;

use App\Infrastructure\Search\MeilisearchIndexConfigurator;
use Illuminate\Console\Command;

final class ConfigureSearchIndexCommand extends Command
{
    protected $signature   = 'search:configure';

    protected $description = 'Apply MeiliSearch index settings (searchable, filterable, sortable attributes)';

    public function __construct(
        private readonly MeilisearchIndexConfigurator $configurator,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Configuring MeiliSearch index...');
        $this->configurator->configure();
        $this->info('Done. Settings applied (indexing in background).');

        return self::SUCCESS;
    }
}
