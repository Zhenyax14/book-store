<?php

namespace App\Presentation\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

final class SyncBookTagsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tag_ids'   => ['present', 'array'],
            'tag_ids.*' => ['required', 'integer', 'min:1'],
        ];
    }
}
