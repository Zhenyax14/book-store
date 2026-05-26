<?php

namespace App\Presentation\Http\Resources\Reading;

use App\Domain\Reading\Entities\UserReadingSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property UserReadingSettings $resource
 */
final class ReadingSettingsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $result = $this->resource;

        return [
            'fontSize'       => $result->fontSize,
            'fontFamily'     => $result->fontFamily->value,
            'lineHeight'     => $result->lineHeight->value,
            'theme'          => $result->theme->value,
            'pageWidth'      => $result->pageWidth,
            'paginationMode' => $result->paginationMode,
            'wordsPerPage'   => $result->wordsPerPage,
        ];
    }
}
