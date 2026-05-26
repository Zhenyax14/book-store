<?php

declare(strict_types=1);

namespace App\Domain\Reading\Interfaces;

use App\Domain\Reading\Entities\UserReadingSettings;

interface UserReadingSettingsRepositoryInterface
{
    public function findByUser(int $userId): ?UserReadingSettings;

    public function save(UserReadingSettings $settings): UserReadingSettings;
}
