<?php

namespace Tests\Feature;

use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StoreSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_store_settings_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.pengaturan'))
            ->assertOk()
            ->assertSee('Informasi Toko')
            ->assertSee('Nama Toko')
            ->assertSee('Alamat Toko')
            ->assertSee('Logo Toko')
            ->assertSee('Email Login Admin')
            ->assertSee('Kata Sandi Baru');
    }

    public function test_admin_can_update_store_name_address_and_logo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->put(route('admin.pengaturan.toko'), [
                'store_name' => 'Jazzel Mart',
                'store_address' => 'Jalan Merdeka, Sabu Raijua',
                'logo' => UploadedFile::fake()->image('logo.png'),
            ])
            ->assertRedirect(route('admin.pengaturan'))
            ->assertSessionHas('storeSettingsStatus');

        $settings = StoreSetting::current();
        $this->assertSame('Jazzel Mart', $settings->store_name);
        $this->assertSame('Jalan Merdeka, Sabu Raijua', $settings->store_address);
        $this->assertNotNull($settings->logo_path);
        Storage::disk('public')->assertExists($settings->logo_path);

        $this->get('/admin')
            ->assertOk()
            ->assertSee('Jazzel Mart')
            ->assertSee(Storage::disk('public')->url($settings->logo_path));

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->get('/login')
            ->assertOk()
            ->assertSee('Jazzel Mart')
            ->assertSee('Jalan Merdeka, Sabu Raijua')
            ->assertSee(Storage::disk('public')->url($settings->logo_path));
    }

    public function test_admin_can_update_login_email_and_password_after_confirming_current_password(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.test',
            'password' => 'current-password',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.pengaturan.akun'), [
                'email' => 'new-admin@example.test',
                'current_password' => 'current-password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertRedirect(route('admin.pengaturan'))
            ->assertSessionHas('accountSettingsStatus');

        $admin->refresh();
        $this->assertSame('new-admin@example.test', $admin->email);
        $this->assertTrue(Hash::check('new-password-123', $admin->password));
    }

    public function test_current_password_is_required_to_change_admin_credentials(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.test',
            'password' => 'current-password',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.pengaturan'))
            ->put(route('admin.pengaturan.akun'), [
                'email' => 'new-admin@example.test',
            ])
            ->assertSessionHasErrorsIn('accountSettings', 'current_password');

        $this->assertSame('admin@example.test', $admin->fresh()->email);
    }

    public function test_cashier_cannot_access_admin_settings(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($cashier)
            ->get(route('admin.pengaturan'))
            ->assertForbidden();
    }
}
