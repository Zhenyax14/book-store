<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class InitiateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'price_id' => [
                'required',
                'string',
                Rule::in(config('services.stripe.subscription_price_ids', [])),
            ],
        ];
    }
}
