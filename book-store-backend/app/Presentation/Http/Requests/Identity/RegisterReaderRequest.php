<?php

namespace App\Presentation\Http\Requests\Identity;

use Illuminate\Foundation\Http\FormRequest;

final class RegisterReaderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:100'],
            'email'                 => ['required', 'string', 'email', 'max:255'],
            'password'              => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'string', 'same:password'],
        ];
    }
}
