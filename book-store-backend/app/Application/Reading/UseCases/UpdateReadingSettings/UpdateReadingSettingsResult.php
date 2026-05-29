<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\UpdateReadingSettings;

use App\Domain\Reading\Entities\UserReadingSettings;

class UpdateReadingSettingsResult
{
    public function __construct(
        public UserReadingSettings $settings,
    ) {}
}
