<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Models;

use App\Domain\Cart\Enums\CartItemTypeEnum;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int              $id
 * @property int              $cart_id
 * @property CartItemTypeEnum $type
 * @property int              $reference_id
 * @property string           $title
 * @property int              $price
 * @property string           $currency
 * @property Carbon           $created_at
 * @property Carbon           $updated_at
 *
 * @property-read CartModel $cart
 *
 * @method static Builder ofType(CartItemTypeEnum $type)
 * @method static Builder forReference(int $referenceId)
 * @method static Builder books()
 * @method static Builder subscriptions()
 */
final class CartItemModel extends Model
{
    protected $table = 'cart_items';

    protected $fillable = [
        'cart_id',
        'type',
        'reference_id',
        'title',
        'price',
        'currency',
    ];

    protected $casts = [
        'type'         => CartItemTypeEnum::class,
        'reference_id' => 'integer',
        'price'        => 'integer',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(CartModel::class, 'cart_id');
    }

    public function scopeOfType(Builder $query, CartItemTypeEnum $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeForReference(Builder $query, int $referenceId): Builder
    {
        return $query->where('reference_id', $referenceId);
    }

    public function scopeBooks(Builder $query): Builder
    {
        return $query->where('type', CartItemTypeEnum::BOOK);
    }

    public function scopeSubscriptions(Builder $query): Builder
    {
        return $query->where('type', CartItemTypeEnum::SUBSCRIPTION);
    }
}
