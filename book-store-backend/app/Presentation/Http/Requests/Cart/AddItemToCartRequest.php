<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Cart;

use App\Domain\Cart\Enums\CartItemTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class AddItemToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'         => ['required', Rule::enum(CartItemTypeEnum::class)],
            'reference_id' => ['required', 'integer', 'min:1'],
        ];
    }
}
