<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Reading;

use App\Domain\Reading\Enums\ReadingStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ListReadingListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'   => ['nullable', 'string', Rule::enum(ReadingStatusEnum::class)],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page'     => ['nullable', 'integer', 'min:1'],
        ];
    }
}
