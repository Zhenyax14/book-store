<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Notification\Enums\NotificationTypeEnum;
use App\Domain\Notification\ValueObjects\NotificationContent;
use App\Domain\Shared\Enums\RoleEnum;
use App\Infrastructure\Notification\UserDatabaseNotification;
use App\Infrastructure\Persistence\Models\BookModel;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Random\RandomException;

final class NotificationSeeder extends Seeder
{
    /**
     * @throws RandomException
     */
    public function run(): void
    {
        /** @var Collection<UserModel> $readers */
        $readers = UserModel::query()
            ->where('role', RoleEnum::READER)
            ->limit(10)
            ->get();

        $publishedBook = BookModel::query()
            ->where('status', 'published')
            ->first();

        $notifiedCount = 0;

        foreach ($readers as $reader) {
            $reader->notify(new UserDatabaseNotification(
                new NotificationContent(
                    type: NotificationTypeEnum::WELCOME,
                    title: 'Welcome to BookStore! 📚',
                    body: "Hello, {$reader->name}! We're glad to see you. Explore thousands of books waiting for you.",
                ),
            ));

            if (null !== $publishedBook) {
                $reader->notify(new UserDatabaseNotification(
                    new NotificationContent(
                        type: NotificationTypeEnum::BOOK_PUBLISHED,
                        title: 'New book available!',
                        body: "The book \"{$publishedBook->title}\" is now available in the catalog.",
                        data: ['book_id' => $publishedBook->id],
                    ),
                ));
            }

            $unread = $reader->unreadNotifications;
            if ($unread->isNotEmpty() && random_int(0, 1)) {
                $unread->first()?->markAsRead();
            }

            $notifiedCount++;
        }

        $this->command->info("Notifications created for {$notifiedCount} readers.");
    }
}
