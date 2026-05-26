<?php

declare(strict_types=1);

namespace App\Application\Reading\UseCases\UpdateReadingSettings;

use App\Domain\Reading\Entities\UserReadingSettings;
use App\Domain\Reading\Interfaces\UserReadingSettingsRepositoryInterface;

final readonly class UpdateReadingSettingsHandler
{
    public function __construct(
        private UserReadingSettingsRepositoryInterface $userReadingSettingsRepository,
    ) {}

    public function handle(UpdateReadingSettingsCommand $command): UpdateReadingSettingsResult
    {
        $settings = new UserReadingSettings(
            id: null,
            userId: $command->userId,
            fontSize: $command->fontSize,
            fontFamily: $command->fontFamily,
            lineHeight: $command->lineHeight,
            theme: $command->theme,
            pageWidth: $command->pageWidth,
            paginationMode: $command->paginationMode,
            wordsPerPage: $command->wordsPerPage,
        );

        $userSettings = $this->userReadingSettingsRepository->save($settings);

        return new UpdateReadingSettingsResult($userSettings);
    }
}
