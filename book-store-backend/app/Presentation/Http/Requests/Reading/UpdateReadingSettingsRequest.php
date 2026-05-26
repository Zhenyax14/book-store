<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Reading;

use App\Domain\Reading\Enums\FontFamilyEnum;
use App\Domain\Reading\Enums\LineHeightEnum;
use App\Domain\Reading\Enums\PaginationModeEnum;
use App\Domain\Reading\Enums\ThemeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateReadingSettingsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'fontSize'       => ['required', 'integer', 'min:14', 'max:28'],
            'fontFamily'     => ['required', 'string', 'max:50', Rule::enum(FontFamilyEnum::class)],
            'lineHeight'     => ['required', 'numeric', Rule::enum(LineHeightEnum::class)],
            'theme'          => ['required', 'string', 'max:20', Rule::enum(ThemeEnum::class)],
            'pageWidth'      => ['required', 'integer', 'in:60,70,90'],
            'paginationMode' => ['required', 'string', 'max:10', Rule::enum(PaginationModeEnum::class)],
            'wordsPerPage'   => ['required', 'integer', 'in:200,300,400'],
        ];
    }
}
