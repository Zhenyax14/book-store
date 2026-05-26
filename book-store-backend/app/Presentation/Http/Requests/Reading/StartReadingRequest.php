<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Reading;

use Illuminate\Foundation\Http\FormRequest;

final class StartReadingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'total_pages' => ['required', 'integer', 'min:1'],
        ];
    }
}
