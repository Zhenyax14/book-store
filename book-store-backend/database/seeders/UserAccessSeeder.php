<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Catalog\Enums\AccessTypeEnum;
use App\Domain\Order\Enums\OrderItemTypeEnum;
use App\Domain\Shared\Enums\RoleEnum;
use App\Domain\Shared\Enums\SubscriptionStatusEnum;
use App\Infrastructure\Persistence\Models\BookModel;
use App\Infrastructure\Persistence\Models\CartItemModel;
use App\Infrastructure\Persistence\Models\CartModel;
use App\Infrastructure\Persistence\Models\UserModel;
use Faker\Factory as Faker;
use Faker\Generator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Random\RandomException;

final class UserAccessSeeder extends Seeder
{
    private const float SUBSCRIPTION_FRACTION = 0.30;

    private const float PURCHASER_FRACTION = 0.55;

    private const float BOOKS_PER_PURCHASER = 2.3;

    /**
     * @throws RandomException
     */
    public function run(): void
    {
        $faker = Faker::create('en');

        $readers = UserModel::query()->where('role', RoleEnum::READER)->get();
        $books = BookModel::where('status', 'published')
            ->whereIn('access_type', [AccessTypeEnum::PURCHASE, AccessTypeEnum::SUBSCRIPTION])
            ->get();

        if ($readers->isEmpty() || $books->isEmpty()) {
            $this->command->error('Reader or Book models are empty.');

            return;
        }

        $subscriptionCount = 0;
        $orderCount = 0;
        $accessCount = 0;

        foreach ($readers as $reader) {
            if ($faker->boolean(self::SUBSCRIPTION_FRACTION * 100)) {
                $this->grantSubscription($reader->id);
                $subscriptionCount++;
            }

            if ($faker->boolean(self::PURCHASER_FRACTION * 100) && $books->isNotEmpty()) {
                $bookCount = $faker->numberBetween(1, 4);
                if ($faker->boolean(40)) {
                    $bookCount = $faker->numberBetween(2, 5);
                }

                $selectedBooks = $books->random(min($bookCount, $books->count()));

                $this->createOrderWithBooks($reader, $selectedBooks, $faker);
                $orderCount++;
                $accessCount += $selectedBooks->count();
            }
        }

        $this->command->info('=== UserAccessSeeder is finished ===');
        $this->command->info("Subscription created: {$subscriptionCount}");
        $this->command->info("Order created: {$orderCount}");
        $this->command->info("Book Access created: {$accessCount}");
    }

    /**
     * @throws RandomException
     */
    private function createOrderWithBooks(UserModel $user, Collection $books, Generator $faker): void
    {
        $checkedOutAt = now()->subDays(random_int(0, 180));

        $cart = CartModel::query()->create([
            'user_id' => $user->id,
            'status' => 'checked_out',
            'checked_out_at' => $checkedOutAt,
            'created_at' => $checkedOutAt->copy()->subMinutes(random_int(5, 120)),
            'updated_at' => $checkedOutAt,
        ]);

        foreach ($books as $book) {
            $price = $book->price ?? random_int(490, 2990);

            CartItemModel::query()->create([
                'cart_id' => $cart->id,
                'type' => OrderItemTypeEnum::BOOK->value,
                'reference_id' => $book->id,
                'title' => $book->title,
                'price' => $price,
                'currency' => 'EUR',
                'created_at' => $checkedOutAt,
                'updated_at' => $checkedOutAt,
            ]);

            $paymentIntentId = 'pi_seed_' . bin2hex(random_bytes(12));

            DB::table('user_book_access')->upsert([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'stripe_payment_intent_id' => $paymentIntentId,
                'granted_at' => $checkedOutAt,
                'created_at' => $checkedOutAt,
                'updated_at' => $checkedOutAt,
            ], ['user_id', 'book_id'], ['stripe_payment_intent_id', 'granted_at']);
        }
    }

    /**
     * @throws RandomException
     */
    private function grantSubscription(int $userId): void
    {
        $exists = DB::table('user_subscriptions')
            ->where('user_id', $userId)
            ->where('status', SubscriptionStatusEnum::ACTIVE->value)
            ->where('expires_at', '>', now())
            ->exists();

        if ($exists) {
            return;
        }

        $startedAt = now()->subDays(random_int(0, 45));

        DB::table('user_subscriptions')->insert([
            'user_id' => $userId,
            'status' => SubscriptionStatusEnum::ACTIVE->value,
            'stripe_subscription_id' => 'sub_seed_' . bin2hex(random_bytes(8)),
            'started_at' => $startedAt,
            'expires_at' => $startedAt->copy()->addMonths(1),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
