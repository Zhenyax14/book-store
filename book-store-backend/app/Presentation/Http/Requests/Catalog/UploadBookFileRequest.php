<?php

namespace App\Presentation\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class UploadBookFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'book_file' => ['required', 'file', 'mimes:pdf,epub', 'max:102400'],
        ];
    }
}
