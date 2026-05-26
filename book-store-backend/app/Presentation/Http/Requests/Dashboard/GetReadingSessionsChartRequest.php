<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Dashboard;

use App\Domain\Dashboard\Enums\PeriodEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class GetReadingSessionsChartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period' => ['sometimes', 'string', Rule::enum(PeriodEnum::class)],
        ];
    }
}
