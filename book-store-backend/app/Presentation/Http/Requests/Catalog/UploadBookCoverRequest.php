<?php

namespace App\Presentation\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class UploadBookCoverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cover' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
