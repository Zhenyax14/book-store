<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Shared\Enums\RoleEnum;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class AdminUserSeeder extends Seeder
{
    private const array ADMINS = [
        [
            'name'  => 'Super Admin',
            'email' => 'admin@bookstore.dev',
            'password' => 'admin123',
        ],
        [
            'name'  => 'Content Manager',
            'email' => 'manager@bookstore.dev',
            'password' => 'manager123',
        ],
    ];

    public function run(): void
    {
        foreach (self::ADMINS as $data) {
            UserModel::query()->firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make($data['password']),
                    'role'     => RoleEnum::ADMIN,
                ],
            );
        }

        $this->command->info('Admins seeded: ' . count(self::ADMINS));
    }
}
