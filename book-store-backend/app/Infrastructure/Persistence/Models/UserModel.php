<?php

namespace App\Infrastructure\Persistence\Models;

use App\Domain\Shared\Enums\RoleEnum;
use Database\Factories\UserModelFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property RoleEnum   $role
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read Collection<int, UserSubscriptionModel> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Collection<int, UserBookAccessModel> $books
 * @property-read int|null $books_count
 */
class UserModel extends Authenticatable
{
    /** @use HasFactory<UserModelFactory> */
    use HasFactory;

    use Notifiable;
    use HasApiTokens;

    protected $table = 'users';

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscriptionModel::class, 'user_id');
    }

    public function books(): HasMany
    {
        return $this->hasMany(UserBookAccessModel::class, 'user_id');
    }

    protected static function newFactory(): UserModelFactory
    {
        return UserModelFactory::new();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => RoleEnum::class,
        ];
    }
}
