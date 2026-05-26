<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests\Subscription;

use App\Domain\Shared\Enums\SubscriptionStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListSubscriptionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'search' => ['nullable', 'string', 'min:3', 'max:255'],
            'status' => ['nullable', 'string', Rule::enum(SubscriptionStatusEnum::class)],
        ];
    }
}
