<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Reading;

use Illuminate\Foundation\Http\FormRequest;

final class SaveReadingProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'chapter_id' => ['required', 'integer', 'exists:book_chapters,id'],
            'page_id' => ['required', 'integer', 'exists:book_pages,id'],
            'global_page_number' => ['required', 'integer', 'min:1'],
            'scroll_position' => ['required', 'integer', 'min:0', 'max:100'],
            'total_pages' => ['required', 'integer', 'min:1'],
            'book_title' => ['required', 'string', 'max:255'],
        ];
    }
}
