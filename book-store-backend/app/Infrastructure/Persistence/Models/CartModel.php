<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Models;

use App\Domain\Cart\Enums\CartStatusEnum;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property CartStatusEnum $status
 * @property CarbonImmutable|null $checked_out_at
 * @property CarbonImmutable $created_at
 *
 * @property-read Collection<CartItemModel> $items
 */
final class CartModel extends Model
{
    protected $table = 'carts';

    protected $fillable = ['user_id', 'status', 'checked_out_at'];

    protected $casts = [
        'status'         => CartStatusEnum::class,
        'checked_out_at' => 'immutable_datetime',
        'created_at'     => 'immutable_datetime',
        'updated_at'     => 'immutable_datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(CartItemModel::class, 'cart_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }
}
