<?php

namespace Tests\Feature\Identity;

use App\Domain\Shared\Enums\RoleEnum;
use App\Infrastructure\Persistence\Models\UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

final class ReaderAuthTest extends TestCase
{
    use DatabaseTransactions;

    // ── Register ──────────────────────────────────────────────────

    public function test_register_returns_201_with_token_and_user(): void
    {
        $this->postJson(route('auth.register'), [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ])
            ->assertStatus(201)
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'name', 'email', 'role'],
            ])
            ->assertJsonFragment(['role' => 'reader']);
    }

    public function test_register_persists_user_to_database(): void
    {
        $this->postJson(route('auth.register'), [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'role'  => 'reader',
        ]);
    }

    public function test_register_returns_409_when_email_already_taken(): void
    {
        UserModel::factory()->create(['email' => 'john@example.com']);

        $this->postJson(route('auth.register'), [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertStatus(409);
    }

    public function test_register_returns_422_on_missing_fields(): void
    {
        $this->postJson(route('auth.register'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    // ── Login ─────────────────────────────────────────────────────

    public function test_login_returns_200_with_token(): void
    {
        UserModel::factory()->create([
            'email'    => 'john@example.com',
            'password' => bcrypt('secret123'),
            'role'     => 'reader',
        ]);

        $this->postJson(route('auth.login'), [
            'email'    => 'john@example.com',
            'password' => 'secret123',
        ])
            ->assertStatus(200)
            ->assertJsonStructure(['token', 'user'])
            ->assertJsonFragment(['role' => 'reader']);
    }

    public function test_login_returns_401_on_wrong_password(): void
    {
        UserModel::factory()->create([
            'email'    => 'john@example.com',
            'password' => bcrypt('secret123'),
            'role'     => 'reader',
        ]);

        $this->postJson(route('auth.login'), [
            'email'    => 'john@example.com',
            'password' => 'wrong',
        ])->assertStatus(401);
    }

    public function test_login_returns_401_for_admin_on_reader_endpoint(): void
    {
        UserModel::factory()->create([
            'email'    => 'admin@example.com',
            'password' => bcrypt('adminpass'),
            'role'     => 'admin',
        ]);

        $this->postJson(route('auth.login'), [
            'email'    => 'admin@example.com',
            'password' => 'adminpass',
        ])->assertStatus(401);
    }

    // ── Logout ────────────────────────────────────────────────────

    public function test_logout_returns_204(): void
    {
        /** @var UserModel $user */
        $user  = UserModel::factory()->create(['role' => RoleEnum::READER]);
        $token = $user->createToken('reader-token')->plainTextToken;

        $this->withToken($token)
            ->postJson(route('auth.logout'))
            ->assertStatus(204);
    }

    public function test_logout_revokes_token(): void
    {
        /** @var UserModel $user */
        $user  = UserModel::factory()->create(['role' => RoleEnum::READER]);
        $token = $user->createToken('reader-token')->plainTextToken;

        $this->withToken($token)
            ->postJson(route('auth.logout'))
            ->assertStatus(204);

        $this->resetAuthState();

        $this->withToken($token)
            ->postJson(route('auth.logout'))
            ->assertStatus(401);
    }

    public function test_logout_requires_auth(): void
    {
        $this->postJson(route('auth.logout'))
            ->assertStatus(401);
    }

    public function test_admin_cannot_access_reader_logout(): void
    {
        /** @var UserModel $admin */
        $admin = UserModel::factory()->create(['role' => 'admin']);

        $this->actingAs($admin, 'sanctum')
            ->postJson(route('auth.logout'))
            ->assertStatus(403);
    }

    private function resetAuthState(): void
    {
        $this->app['auth']->forgetGuards();
    }
}
