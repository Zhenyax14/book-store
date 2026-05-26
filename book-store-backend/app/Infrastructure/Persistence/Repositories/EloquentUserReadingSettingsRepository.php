<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Reading\Entities\UserReadingSettings;
use App\Domain\Reading\Interfaces\UserReadingSettingsRepositoryInterface;
use App\Infrastructure\Persistence\Models\ReadingSettingsModel;

class EloquentUserReadingSettingsRepository implements UserReadingSettingsRepositoryInterface
{
    public function findByUser(int $userId): ?UserReadingSettings
    {
        return $this->toDomain(
            ReadingSettingsModel::query()->where('user_id', $userId)->first(),
        );
    }

    public function save(UserReadingSettings $settings): UserReadingSettings
    {
        $userSettings = ReadingSettingsModel::query()->where('user_id', $settings->userId)
            ->updateOrCreate(
                ['user_id' => $settings->userId],
                $settings->toArray(),
            );

        return $this->toDomain($userSettings);
    }

    private function toDomain(?ReadingSettingsModel $model = null): ?UserReadingSettings
    {
        if (null === $model) {
            return null;
        }

        return new UserReadingSettings(
            id: $model->id,
            userId: $model->user_id,
            fontSize: $model->font_size,
            fontFamily: $model->font_family,
            lineHeight: $model->line_height,
            theme: $model->theme,
            pageWidth: $model->page_width,
            paginationMode: $model->pagination_mode,
            wordsPerPage: $model->words_per_page,
        );
    }
}
