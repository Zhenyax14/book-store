<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Catalog;

use App\Domain\Catalog\Enums\AccessTypeEnum;
use App\Domain\Catalog\Enums\BookStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class BookSearchRequest extends FormRequest
{
    private const int MIN_QUERY_LENGTH = 1;

    private const int MAX_QUERY_LENGTH = 100;

    private const int MIN_LIMIT        = 1;

    private const int MAX_LIMIT        = 100;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // q — поисковый запрос. nullable: пустой запрос = "показать всё"
            // (MeiliSearch с пустой строкой возвращает все документы)
            'q'           => ['nullable', 'string', 'min:' . self::MIN_QUERY_LENGTH, 'max:' . self::MAX_QUERY_LENGTH],

            // Фильтры — все опциональны, валидируем через Rule::enum
            'status'      => ['nullable', 'string', Rule::enum(BookStatusEnum::class)],
            'access_type' => ['nullable', 'string', Rule::enum(AccessTypeEnum::class)],

            // ISO 639-1: ровно 2 строчных буквы — 'en', 'ru', 'de'
            'language'    => ['nullable', 'string', 'size:2', 'regex:/^[a-z]{2}$/'],

            // Пагинация — limit/offset, а не page/per_page,
            // потому что MeiliSearch работает именно с offset-based пагинацией
            'limit'       => ['nullable', 'integer', 'min:' . self::MIN_LIMIT, 'max:' . self::MAX_LIMIT],
            'offset'      => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'q.max'           => 'Search query must not exceed :max characters.',
            'language.size'   => 'Language must be a 2-letter ISO 639-1 code (e.g. "en", "ru").',
            'language.regex'  => 'Language must contain only lowercase letters.',
            'limit.max'       => 'Limit must not exceed :max results per request.',
            'offset.min'      => 'Offset must be a non-negative integer.',
        ];
    }
}
