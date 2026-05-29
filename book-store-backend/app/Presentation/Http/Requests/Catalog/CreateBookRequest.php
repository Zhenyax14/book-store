<?php

namespace App\Presentation\Http\Requests\Catalog;

use App\Domain\Catalog\Enums\AccessTypeEnum;
use App\Domain\Shared\ValueObjects\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'isbn'           => ['nullable', 'string', 'max:20', 'unique:books,isbn'],
            'language'       => ['required', 'string', 'size:2', 'regex:/^[a-z]{2}$/i'],
            'publisher'      => ['nullable', 'string', 'max:255'],
            'published_year' => ['nullable', 'integer', 'min:1000', 'max:' . date('Y')],
            'access_type'    => ['required', Rule::enum(AccessTypeEnum::class)],
            'price'          => ['required', 'integer', 'min:0'],
            'currency'       => ['required', 'string', Rule::in(Currency::SUPPORTED)],
        ];
    }
}
