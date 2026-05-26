<?php

namespace App\Presentation\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class ListTagsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'page' => ['nullable', 'numeric', 'min:1'],
        ];
    }
}
