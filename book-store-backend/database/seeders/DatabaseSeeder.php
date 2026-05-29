<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Taxonomy — must come before books
            TagSeeder::class,

            // 2. Users — required by all activity seeders
            AdminUserSeeder::class,
            ReaderUserSeeder::class,

            // 3. Catalog — books with tag relationships
            BookSeeder::class,

            // 4. Book content — chapters and pages for published books
            BookContentSeeder::class,

            // 5. Reader activity — reading list, sessions, progress
            ReadingActivitySeeder::class,

            // 6. Commerce — subscriptions and book purchases
            UserAccessSeeder::class,

            // 7. Notifications
            NotificationSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('✅ Database seeding complete.');
        $this->command->newLine();
        $this->command->table(
            ['Account', 'Email', 'Password', 'Role'],
            [
                ['Super Admin',    'admin@bookstore.dev',   'admin123',  'admin'],
                ['Content Manager','manager@bookstore.dev', 'manager123','admin'],
                ['Alice Reader',   'alice@bookstore.dev',   'reader123', 'reader'],
                ['Bob Bookworm',   'bob@bookstore.dev',     'reader123', 'reader'],
                ['Carol Pages',    'carol@bookstore.dev',   'reader123', 'reader'],
            ],
        );
    }
}
