<?php

namespace Tests\Feature\Notification;

use App\Domain\Notification\Enums\NotificationTypeEnum;
use App\Domain\Notification\ValueObjects\NotificationContent;
use App\Infrastructure\Notification\UserDatabaseNotification;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;

final class NotificationControllerTest extends TestCase
{
    use DatabaseTransactions;

    private UserModel $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserModel::factory()->create();
        $this->token = $this->user->createToken('reader-token')->plainTextToken;
    }

    public function test_index_returns_200_with_correct_structure(): void
    {
        $this->withToken($this->token)
            ->getJson(route('notifications.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'total',
                    'per_page',
                    'current_page',
                    'total_pages',
                    'unread_count',
                ],
            ]);
    }

    public function test_index_returns_empty_data_for_new_user(): void
    {
        $this->withToken($this->token)
            ->getJson(route('notifications.index'))
            ->assertOk()
            ->assertJsonFragment(['total' => 0, 'unread_count' => 0])
            ->assertJsonFragment(['data' => []]);
    }

    public function test_index_returns_notifications_for_current_user(): void
    {
        $this->sendNotification($this->user);
        $this->sendNotification($this->user);

        $this->withToken($this->token)
            ->getJson(route('notifications.index'))
            ->assertOk()
            ->assertJsonFragment(['total' => 2]);
    }

    public function test_index_does_not_return_other_users_notifications(): void
    {
        /** @var UserModel $other */
        $other = UserModel::factory()->create();
        $this->sendNotification($other);

        $this->withToken($this->token)
            ->getJson(route('notifications.index'))
            ->assertJsonFragment(['total' => 0]);
    }

    public function test_index_notification_has_expected_fields(): void
    {
        $this->sendNotification($this->user, NotificationTypeEnum::WELCOME);

        $response = $this->withToken($this->token)
            ->getJson(route('notifications.index'));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'type',
                    'title',
                    'body',
                    'data',
                    'is_read',
                    'read_at',
                    'created_at',
                ]],
            ])
            ->assertJsonFragment([
                'type' => NotificationTypeEnum::WELCOME->value,
                'is_read' => false,
                'read_at' => null,
            ]);
    }

    public function test_index_respects_per_page_parameter(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->sendNotification($this->user);
        }

        $this->withToken($this->token)
            ->getJson(route('notifications.index', ['per_page' => 2]))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['total' => 5, 'total_pages' => 3]);
    }

    public function test_index_requires_auth(): void
    {
        $this->getJson(route('notifications.index'))
            ->assertUnauthorized();
    }

    public function test_unread_count_returns_correct_number(): void
    {
        $this->sendNotification($this->user);
        $this->sendNotification($this->user);

        $this->withToken($this->token)
            ->getJson(route('notifications.unread-count'))
            ->assertOk()
            ->assertExactJson(['count' => 2]);
    }

    public function test_unread_count_returns_zero_when_all_read(): void
    {
        $notification = $this->sendNotification($this->user);
        $notification->markAsRead();

        $this->withToken($this->token)
            ->getJson(route('notifications.unread-count'))
            ->assertOk()
            ->assertExactJson(['count' => 0]);
    }

    public function test_unread_count_requires_auth(): void
    {
        $this->getJson(route('notifications.unread-count'))
            ->assertUnauthorized();
    }

    public function test_mark_as_read_returns_204(): void
    {
        $notification = $this->sendNotification($this->user);

        $this->withToken($this->token)
            ->patchJson(route('notifications.read', ['id' => $notification->id]))
            ->assertNoContent();
    }

    public function test_mark_as_read_persists_read_at(): void
    {
        $notification = $this->sendNotification($this->user);

        $this->withToken($this->token)
            ->patchJson(route('notifications.read', ['id' => $notification->id]));

        $this->assertDatabaseMissing('notifications', [
            'id' => $notification->id,
            'read_at' => null,
        ]);
    }

    public function test_mark_as_read_returns_404_for_nonexistent_notification(): void
    {
        $this->withToken($this->token)
            ->patchJson(route('notifications.read', ['id' => '00000000-0000-0000-0000-000000000000']))
            ->assertNotFound();
    }

    public function test_mark_as_read_returns_404_for_another_users_notification(): void
    {
        /** @var UserModel $other */
        $other = UserModel::factory()->create();
        $notification = $this->sendNotification($other);

        $this->withToken($this->token)
            ->patchJson(route('notifications.read', ['id' => $notification->id]))
            ->assertNotFound();

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'read_at' => null,
        ]);
    }

    public function test_mark_as_read_requires_auth(): void
    {
        $this->patchJson(route('notifications.read', ['id' => 'any-uuid']))
            ->assertUnauthorized();
    }

    public function test_mark_all_as_read_returns_204(): void
    {
        $this->sendNotification($this->user);
        $this->sendNotification($this->user);

        $this->withToken($this->token)
            ->postJson(route('notifications.read-all'))
            ->assertNoContent();
    }

    public function test_mark_all_as_read_marks_only_current_users_notifications(): void
    {
        /** @var UserModel $other */
        $other = UserModel::factory()->create();
        $ownNotif = $this->sendNotification($this->user);
        $otherNotif = $this->sendNotification($other);

        $this->withToken($this->token)
            ->postJson(route('notifications.read-all'));

        $this->assertDatabaseMissing('notifications', [
            'id' => $ownNotif->id,
            'read_at' => null,
        ]);

        $this->assertDatabaseHas('notifications', [
            'id' => $otherNotif->id,
            'read_at' => null,
        ]);
    }

    public function test_mark_all_as_read_succeeds_even_when_no_notifications(): void
    {
        $this->withToken($this->token)
            ->postJson(route('notifications.read-all'))
            ->assertNoContent();
    }

    public function test_unread_count_drops_to_zero_after_mark_all_as_read(): void
    {
        $this->sendNotification($this->user);
        $this->sendNotification($this->user);

        $this->withToken($this->token)
            ->postJson(route('notifications.read-all'));

        $this->withToken($this->token)
            ->getJson(route('notifications.unread-count'))
            ->assertExactJson(['count' => 0]);
    }

    public function test_mark_all_as_read_requires_auth(): void
    {
        $this->postJson(route('notifications.read-all'))
            ->assertUnauthorized();
    }

    private function sendNotification(
        UserModel            $user,
        NotificationTypeEnum $type = NotificationTypeEnum::BOOK_PUBLISHED,
    ): DatabaseNotification {
        $user->notify(new UserDatabaseNotification(
            new NotificationContent(
                type: $type,
                title: 'Test: ' . $type->value,
                body: 'Test body',
                data: ['book_id' => 1],
            ),
        ));

        return $user->notifications()->latest()->first();
    }
}
