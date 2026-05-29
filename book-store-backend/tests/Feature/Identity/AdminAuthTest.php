<?php

namespace Tests\Feature\Identity;

use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

final class AdminAuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_returns_200_with_token(): void
    {
        UserModel::factory()->create([
            'email'    => 'admin@example.com',
            'password' => bcrypt('adminpass'),
            'role'     => 'admin',
        ]);

        $this->postJson(route('admin.auth.login'), [
            'email'    => 'admin@example.com',
            'password' => 'adminpass',
        ])
            ->assertStatus(200)
            ->assertJsonStructure(['token', 'user'])
            ->assertJsonFragment(['role' => 'admin']);
    }

    public function test_login_returns_401_for_reader_on_admin_endpoint(): void
    {
        UserModel::factory()->create([
            'email'    => 'reader@example.com',
            'password' => bcrypt('pass'),
            'role'     => 'reader',
        ]);

        $this->postJson(route('admin.auth.login'), [
            'email'    => 'reader@example.com',
            'password' => 'pass',
        ])->assertStatus(401);
    }

    public function test_login_returns_401_on_wrong_password(): void
    {
        UserModel::factory()->create([
            'email'    => 'admin@example.com',
            'password' => bcrypt('adminpass'),
            'role'     => 'admin',
        ]);

        $this->postJson(route('admin.auth.login'), [
            'email'    => 'admin@example.com',
            'password' => 'wrong',
        ])->assertStatus(401);
    }

    public function test_logout_returns_204(): void
    {
        /** @var UserModel $admin */
        $admin = UserModel::factory()->create(['role' => 'admin']);

        $this->actingAs($admin, 'sanctum')
            ->postJson(route('admin.auth.logout'))
            ->assertStatus(204);
    }

    public function test_reader_cannot_access_admin_logout(): void
    {
        /** @var UserModel $reader */
        $reader = UserModel::factory()->create(['role' => 'reader']);

        $this->actingAs($reader, 'sanctum')
            ->postJson(route('admin.auth.logout'))
            ->assertStatus(403);
    }
}
