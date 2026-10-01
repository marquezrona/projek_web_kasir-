<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_can_login_and_is_redirected_to_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin Astrowisata',
            'email' => 'admin@astrowisata.test',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@astrowisata.test',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
        $this->get('/admin')->assertOk();
    }

    public function test_cashier_can_access_cashier_page_but_not_admin_dashboard(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($cashier)->get('/kasir')->assertOk();
        $this->actingAs($cashier)->get('/admin')->assertForbidden();
    }
}
