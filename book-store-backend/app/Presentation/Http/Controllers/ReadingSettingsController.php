<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Reading\UseCases\GetReadingSettings\GetReadingSettingsCommand;
use App\Application\Reading\UseCases\GetReadingSettings\GetReadingSettingsHandler;
use App\Application\Reading\UseCases\UpdateReadingSettings\UpdateReadingSettingsCommand;
use App\Application\Reading\UseCases\UpdateReadingSettings\UpdateReadingSettingsHandler;
use App\Domain\Reading\Enums\FontFamilyEnum;
use App\Domain\Reading\Enums\LineHeightEnum;
use App\Domain\Reading\Enums\PaginationModeEnum;
use App\Domain\Reading\Enums\ThemeEnum;
use App\Presentation\Http\Requests\Reading\UpdateReadingSettingsRequest;
use App\Presentation\Http\Resources\Reading\ReadingSettingsResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ReadingSettingsController extends Controller
{
    public function __construct(
        private readonly GetReadingSettingsHandler $getReadingSettingsHandler,
        private readonly UpdateReadingSettingsHandler $updateReadingSettingsHandler,
    ) {}

    /**
     * @param Request $request
     * @return JsonResponse
     * @response array{
     *     fontSize: int,
     *     fontFamily: App\Domain\Catalog\Enums\FontFamilyEnum,
     *     lineHeight: App\Domain\Catalog\Enums\LineHeightEnum,
     *     pageWidth: int,
     *     theme: App\Domain\Reading\Enums\ThemeEnum,
     *     paginationMode: App\Domain\Reading\Enums\PaginationModeEnum,
     *     wordsPerPage: int,
     * }
     */
    public function show(Request $request): JsonResponse
    {
        $command = new GetReadingSettingsCommand($request->user()->id);

        $result = $this->getReadingSettingsHandler->handle($command);

        return new JsonResponse(
            new ReadingSettingsResource($result->settings),
        );
    }

    /**
     * @param UpdateReadingSettingsRequest $request
     * @return JsonResponse
     * @response array{
     *     fontSize: int,
     *     fontFamily: App\Domain\Catalog\Enums\FontFamilyEnum,
     *     lineHeight: App\Domain\Catalog\Enums\LineHeightEnum,
     *     pageWidth: int,
     *     theme: App\Domain\Reading\Enums\ThemeEnum,
     *     paginationMode: App\Domain\Reading\Enums\PaginationModeEnum,
     *     wordsPerPage: int,
     * }
     */
    public function update(UpdateReadingSettingsRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $command = new UpdateReadingSettingsCommand(
            userId: $request->user()->id,
            fontSize: $validated['fontSize'],
            fontFamily: FontFamilyEnum::from($validated['fontFamily']),
            lineHeight: LineHeightEnum::from($validated['lineHeight']),
            theme: ThemeEnum::from($validated['theme']),
            pageWidth: $validated['pageWidth'],
            paginationMode: PaginationModeEnum::from($validated['paginationMode']),
            wordsPerPage: $validated['wordsPerPage'],
        );

        $result = $this->updateReadingSettingsHandler->handle($command);

        return new JsonResponse(
            new ReadingSettingsResource($result->settings),
        );
    }
}
