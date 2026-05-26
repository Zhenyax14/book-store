<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Cart;

use App\Domain\Shared\ValueObjects\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currency' => ['required', 'string', Rule::in(Currency::SUPPORTED)],
        ];
    }
}
