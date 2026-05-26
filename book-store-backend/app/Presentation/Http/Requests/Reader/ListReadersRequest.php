<?php

namespace App\Presentation\Http\Requests\Reader;

use App\Domain\User\Enums\ReaderFilterEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListReadersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filter' => ['nullable', 'string', Rule::enum(ReaderFilterEnum::class)],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'search' => ['nullable', 'string', 'min:3', 'max:255'],
        ];
    }
}
