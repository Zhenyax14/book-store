<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\GetReadingSettings;

use App\Domain\Reading\Entities\UserReadingSettings;
use App\Domain\Reading\Interfaces\UserReadingSettingsRepositoryInterface;

final readonly class GetReadingSettingsHandler
{
    public function __construct(private UserReadingSettingsRepositoryInterface $userReadingSettingsRepository) {}

    public function handle(GetReadingSettingsCommand $command): GetReadingSettingsResult
    {
        $userSettings = $this->userReadingSettingsRepository->findByUser($command->userId);

        if (null === $userSettings) {
            return new GetReadingSettingsResult(UserReadingSettings::defaults($command->userId));
        }

        return new GetReadingSettingsResult($userSettings);
    }
}
