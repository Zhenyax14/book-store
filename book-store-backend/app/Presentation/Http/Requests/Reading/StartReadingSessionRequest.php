<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Reading;

use Illuminate\Foundation\Http\FormRequest;

final class StartReadingSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_page_id' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
