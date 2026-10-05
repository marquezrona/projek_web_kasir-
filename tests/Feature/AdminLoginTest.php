<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_can_login_and_is_redirected_to_admin_dashboard(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('assets/img/jazzel-logo.jpg');

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
        $this->get('/admin')
            ->assertOk()
            ->assertSee('aria-label="Menu admin"', false)
            ->assertSee('Kelola Barang')
            ->assertSee('>Keluar</span>', false)
            ->assertSee('Konfirmasi Keluar')
            ->assertSee('Ya, Keluar')
            ->assertDontSee('Mode Kasir')
            ->assertSee('Pengaturan')
            ->assertSee('assets/img/jazzel-logo.jpg')
            ->assertDontSee('Database Barang');
        $this->get('/admin')
            ->assertOk()
            ->assertSee('Grafik penjualan tujuh hari terakhir')
            ->assertSee('Belum ada transaksi dalam tujuh hari terakhir.');
        $this->get('/products')
            ->assertOk()
            ->assertSee('Daftar Barang')
            ->assertSee('Tambah Barang')
            ->assertSee('Cari nama, kategori, atau deskripsi barang...')
            ->assertSee('Belum ada barang yang terdaftar.')
            ->assertSee('Konfirmasi Hapus Barang')
            ->assertSee('Ya, Hapus Barang')
            ->assertDontSee('onsubmit="return confirm', false)
            ->assertDontSee('Status');
    }

    public function test_admin_can_search_products_by_name_category_or_description(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Product::query()->create([
            'name' => 'Beras Premium',
            'category' => 'Sembako',
            'description' => 'Beras lokal kualitas pilihan',
            'price' => 18000,
            'stock' => 12,
            'is_active' => true,
        ]);
        Product::query()->create([
            'name' => 'Sabun Mandi',
            'category' => 'Perawatan',
            'description' => 'Sabun wangi',
            'price' => 5000,
            'stock' => 8,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get('/products?search=Premium')
            ->assertOk()
            ->assertSee('Beras Premium')
            ->assertDontSee('Sabun Mandi')
            ->assertSee('1 hasil pencarian');

        $this->actingAs($admin)
            ->get('/products?search=Perawatan')
            ->assertOk()
            ->assertSee('Sabun Mandi')
            ->assertDontSee('Beras Premium');

        $this->actingAs($admin)
            ->get('/products?search=kualitas pilihan')
            ->assertOk()
            ->assertSee('Beras Premium')
            ->assertDontSee('Sabun Mandi');
    }

    public function test_product_delete_button_requires_confirmation_for_the_selected_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::query()->create([
            'name' => 'Beras Premium',
            'category' => 'Sembako',
            'price' => 18000,
            'stock' => 12,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get('/products')
            ->assertOk()
            ->assertSee('data-bs-target="#deleteProductModal"', false)
            ->assertSee('data-delete-url="'.route('products.destroy', $product).'"', false)
            ->assertSee('data-product-name="Beras Premium"', false);

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_new_product_is_active_and_available_to_cashier_by_default(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/products/create')
            ->assertOk()
            ->assertSee('Aktif dan tampil di transaksi kasir')
            ->assertSee('checked', false);

        $this->actingAs($admin)->post('/products', [
            'name' => 'Beras Baru',
            'category' => 'Sembako',
            'description' => 'Produk untuk dijual',
            'price' => 19000,
            'stock' => 10,
        ])->assertRedirect('/products');

        $product = Product::query()->where('name', 'Beras Baru')->firstOrFail();
        $this->assertTrue($product->is_active);

        $cashier = User::factory()->create(['role' => 'kasir']);
        $this->actingAs($cashier)
            ->get('/kasir')
            ->assertOk()
            ->assertSee('Beras Baru');
        $this->actingAs($cashier)
            ->get('/kasir/produk')
            ->assertOk()
            ->assertSee('Beras Baru');
    }

    public function test_unchecked_product_active_option_keeps_product_hidden_from_cashier(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post('/products', [
            'name' => 'Barang Nonaktif',
            'category' => 'Lainnya',
            'price' => 1000,
            'stock' => 3,
            'is_active' => '0',
        ])->assertRedirect('/products');

        $product = Product::query()->where('name', 'Barang Nonaktif')->firstOrFail();
        $this->assertFalse($product->is_active);

        $cashier = User::factory()->create(['role' => 'kasir']);
        $this->actingAs($cashier)
            ->get('/kasir')
            ->assertOk()
            ->assertDontSee('Barang Nonaktif');

        $this->actingAs($admin)->put("/products/{$product->id}", [
            'name' => $product->name,
            'category' => $product->category,
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $product->stock,
            'is_active' => '1',
        ])->assertRedirect('/products');

        $this->assertTrue($product->fresh()->is_active);
        $this->actingAs($cashier)
            ->get('/kasir/produk')
            ->assertOk()
            ->assertSee('Barang Nonaktif');
    }

    public function test_cashier_can_access_cashier_page_but_not_admin_dashboard(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($cashier)
            ->get('/kasir')
            ->assertOk()
            ->assertSee('Transaksi Baru')
            ->assertSee('aria-label="Menu kasir"', false)
            ->assertSee('Toko Sembako Jazzel')
            ->assertSee('Konfirmasi Keluar')
            ->assertSee('data-method="tunai"', false)
            ->assertSee('data-method="qris"', false)
            ->assertSee('data-method="transfer"', false)
            ->assertSee('SIMULASI - BUKAN PEMBAYARAN SUNGGUHAN')
            ->assertDontSee('data-method="debit"', false)
            ->assertSee('assets/img/jazzel-logo.jpg')
            ->assertSee('Belum ada barang aktif dengan stok tersedia.');

        $this->actingAs($cashier)->get('/kasir/produk')
            ->assertOk()
            ->assertSee('Daftar Produk')
            ->assertSee('Belum ada produk aktif.');
        $this->actingAs($cashier)->get('/kasir/laporan')->assertOk()->assertSee('Laporan Kasir');
        $this->actingAs($cashier)->get('/admin')->assertForbidden();
    }

    public function test_admin_cannot_access_cashier_mode(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/kasir')->assertForbidden();
        $this->actingAs($admin)->get('/kasir/produk')->assertForbidden();
        $this->actingAs($admin)->get('/kasir/riwayat')->assertForbidden();
        $this->actingAs($admin)->get('/kasir/laporan')->assertForbidden();
        $this->actingAs($admin)->postJson('/kasir/transaksi', [])->assertForbidden();
    }

    public function test_admin_can_create_a_cashier_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.kasir'))
            ->assertOk()
            ->assertSee('Tambah Akun Kasir')
            ->assertSee('Simpan Akun Kasir')
            ->assertSee('aria-expanded="false"', false)
            ->assertSee('id="tambah-akun-kasir"', false)
            ->assertSee('hidden', false);

        $this->actingAs($admin)
            ->post(route('admin.kasir.store'), [
                'name' => 'Kasir Baru',
                'email' => 'kasir.baru@example.test',
                'password' => 'kasir-password-123',
                'password_confirmation' => 'kasir-password-123',
            ])
            ->assertRedirect(route('admin.kasir'))
            ->assertSessionHas('cashierCreated');

        $cashier = User::query()->where('email', 'kasir.baru@example.test')->firstOrFail();
        $this->assertSame('Kasir Baru', $cashier->name);
        $this->assertSame('kasir', $cashier->role);
        $this->assertNotNull($cashier->email_verified_at);
        $this->assertTrue(Hash::check('kasir-password-123', $cashier->password));

        $this->actingAs($cashier)
            ->get('/kasir')
            ->assertOk()
            ->assertSee('Transaksi Baru');
    }

    public function test_cashier_account_creation_rejects_duplicate_email_and_unconfirmed_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['email' => 'existing@example.test', 'role' => 'kasir']);

        $this->actingAs($admin)
            ->from(route('admin.kasir'))
            ->post(route('admin.kasir.store'), [
                'name' => 'Kasir Gagal',
                'email' => 'existing@example.test',
                'password' => 'kasir-password-123',
                'password_confirmation' => 'password-berbeda',
            ])
            ->assertSessionHasErrorsIn('createCashier', ['email', 'password']);

        $this->assertDatabaseCount('users', 2);
        $this->get(route('admin.kasir'))
            ->assertOk()
            ->assertSee('aria-expanded="true"', false);
    }

    public function test_cashier_cannot_create_another_user_account(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($cashier)
            ->post(route('admin.kasir.store'), [
                'name' => 'Akun Baru',
                'email' => 'akun.baru@example.test',
                'password' => 'password-baru-123',
                'password_confirmation' => 'password-baru-123',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'akun.baru@example.test']);
    }
}
