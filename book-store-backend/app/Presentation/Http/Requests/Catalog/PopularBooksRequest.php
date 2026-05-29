<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Catalog;

use App\Domain\Catalog\Enums\PopularityPeriodEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PopularBooksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period'   => ['sometimes', 'string', Rule::enum(PopularityPeriodEnum::class)],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page'     => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
