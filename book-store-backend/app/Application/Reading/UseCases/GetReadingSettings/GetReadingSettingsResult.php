<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetReadingSettings;

use App\Domain\Reading\Entities\UserReadingSettings;

final readonly class GetReadingSettingsResult
{
    public function __construct(
        public UserReadingSettings $settings,
    ) {}
}
