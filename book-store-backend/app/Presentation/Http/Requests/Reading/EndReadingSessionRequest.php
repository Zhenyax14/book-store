<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Reading;

use Illuminate\Foundation\Http\FormRequest;

final class EndReadingSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'end_page_id'      => ['required', 'integer', 'min:1'],
            'duration_seconds' => ['required', 'integer', 'min:0'],
        ];
    }
}
