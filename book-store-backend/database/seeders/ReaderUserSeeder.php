<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Shared\Enums\RoleEnum;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class ReaderUserSeeder extends Seeder
{
    /** Fixed demo accounts always present in every environment */
    private const array DEMO_READERS = [
        ['name' => 'Alice Reader',  'email' => 'alice@bookstore.dev',  'password' => 'reader123'],
        ['name' => 'Bob Bookworm',  'email' => 'bob@bookstore.dev',    'password' => 'reader123'],
        ['name' => 'Carol Pages',   'email' => 'carol@bookstore.dev',  'password' => 'reader123'],
    ];

    private const int FAKER_COUNT = 20;

    public function run(): void
    {
        foreach (self::DEMO_READERS as $data) {
            UserModel::query()->firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make($data['password']),
                    'role'     => RoleEnum::READER,
                ],
            );
        }

        UserModel::factory()
            ->count(self::FAKER_COUNT)
            ->create(['role' => RoleEnum::READER]);

        $total = count(self::DEMO_READERS) + self::FAKER_COUNT;
        $this->command->info("Readers seeded: {$total}");
    }
}
