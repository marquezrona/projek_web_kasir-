<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_a_token_without_assigning_an_admin_role(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Kasir API',
            'email' => 'kasir@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'admin',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('user.role', 'kasir')
            ->assertJsonMissingPath('user.password')
            ->assertJsonStructure(['user' => ['id', 'name', 'email', 'role'], 'token']);

        $this->assertDatabaseHas('users', [
            'email' => 'kasir@example.com',
            'role' => 'kasir',
        ]);
    }

    public function test_user_can_login_get_profile_and_revoke_their_token(): void
    {
        $user = User::factory()->create([
            'email' => 'kasir@example.com',
            'password' => 'Password123!',
            'role' => 'kasir',
        ]);

        $token = $this->postJson('/api/login', [
            'email' => 'kasir@example.com',
            'password' => 'Password123!',
        ])->assertOk()->json('token');

        $this->withToken($token)
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('id', $user->id);

        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);

        Auth::forgetGuards();

        $this->withToken($token)
            ->getJson('/api/user')
            ->assertUnauthorized();
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        $this->postJson('/api/login', [
            'email' => 'missing@example.com',
            'password' => 'wrong-password',
        ])->assertUnauthorized();
    }
}
