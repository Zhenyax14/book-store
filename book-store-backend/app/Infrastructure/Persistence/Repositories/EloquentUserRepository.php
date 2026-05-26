<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Identity\Entities\User;
use App\Domain\Identity\Interfaces\UserRepositoryInterface;
use App\Domain\Identity\ValueObjects\Email;
use App\Domain\Identity\ValueObjects\HashedPassword;
use App\Domain\Identity\ValueObjects\UserId;
use App\Infrastructure\Persistence\Models\UserModel;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function save(User $user): User
    {
        if (null === $user->getId()) {
            $model = UserModel::query()->create($this->toArray($user));
        } else {
            $model = UserModel::query()->findOrFail($user->getId()->value);
            $model->update($this->toArray($user));
        }

        return $this->toDomain($model);
    }

    public function findByEmail(Email $email): ?User
    {
        $model = UserModel::query()
            ->where('email', $email->value)
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function existsByEmail(Email $email): bool
    {
        return UserModel::query()
            ->where('email', $email->value)
            ->exists();
    }

    private function toArray(User $user): array
    {
        return [
            'name'     => $user->getName(),
            'email'    => $user->getEmail()->value,
            'password' => $user->getPassword()->value,
            'role'     => $user->getRole()->value,
        ];
    }

    private function toDomain(UserModel $model): User
    {
        return new User(
            id: new UserId($model->id),
            name: $model->name,
            email: new Email($model->email),
            password: new HashedPassword($model->password),
            role: $model->role,
        );
    }
}
