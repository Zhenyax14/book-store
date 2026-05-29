<?php

namespace App\Presentation\Http\Requests\Catalog;

use App\Domain\Catalog\Enums\AccessTypeEnum;
use App\Domain\Catalog\Enums\BookStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListBooksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', Rule::enum(BookStatusEnum::class)],
            'access_type' => ['nullable', 'string', Rule::enum(AccessTypeEnum::class)],
            'language' => ['nullable', 'string', 'size:2', 'regex:/^[a-z]{2}$/'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'search' => ['nullable', 'string', 'min:3', 'max:255'],
            'tag' => ['nullable', 'string', 'exists:tags,slug'],
            ];
    }
}
