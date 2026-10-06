<?php

namespace Tests\Feature;

use App\Contracts\ReceiptPrinter;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CashierTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_checkout_saves_sale_and_decreases_stock(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $product = Product::query()->create([
            'name' => 'Beras',
            'category' => 'Sembako',
            'price' => 15000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->actingAs($cashier)->postJson(route('cashier.checkout'), [
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 2,
                'price' => 1,
            ]],
            'customer_type' => 'Umum',
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'paid' => 40000,
            'payment_method' => 'tunai',
        ]);

        $response->assertCreated()
            ->assertJsonPath('total', '30000.00')
            ->assertJsonPath('change', '10000.00');

        $sale = Sale::query()->with('items')->firstOrFail();
        $this->assertSame($cashier->id, $sale->cashier_id);
        $this->assertSame('Beras', $sale->items->first()->product_name);
        $this->assertSame(2, $sale->items->first()->quantity);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
    }

    public function test_checkout_rejects_insufficient_payment_without_changing_stock(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $product = Product::query()->create([
            'name' => 'Minyak Goreng',
            'price' => 20000,
            'stock' => 4,
            'is_active' => true,
        ]);

        $this->actingAs($cashier)->postJson(route('cashier.checkout'), [
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
            'customer_type' => 'Umum',
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'paid' => 1000,
            'payment_method' => 'tunai',
        ])->assertUnprocessable()->assertJsonValidationErrors('paid');

        $this->assertDatabaseCount('sales', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 4]);
    }

    public function test_qris_checkout_records_a_simulated_exact_payment(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $product = Product::query()->create([
            'name' => 'Teh Botol',
            'price' => 7000,
            'stock' => 3,
            'is_active' => true,
        ]);

        $this->actingAs($cashier)->postJson(route('cashier.checkout'), [
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
            'customer_type' => 'Umum',
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'paid' => 14000,
            'payment_method' => 'qris',
        ])->assertCreated()
            ->assertJsonPath('total', '14000.00')
            ->assertJsonPath('change', '0.00');

        $this->assertDatabaseHas('sales', [
            'payment_method' => 'qris',
            'paid' => 14000,
            'total' => 14000,
        ]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
    }

    public function test_checkout_only_accepts_cash_qris_or_bank_transfer(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $product = Product::query()->create([
            'name' => 'Gula',
            'price' => 10000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->actingAs($cashier)->postJson(route('cashier.checkout'), [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'customer_type' => 'Umum',
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'paid' => 10000,
            'payment_method' => 'debit',
        ])->assertUnprocessable()->assertJsonValidationErrors('payment_method');

        $this->assertDatabaseCount('sales', 0);
        $this->actingAs($cashier)->postJson(route('cashier.checkout'), [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'customer_type' => 'Umum',
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'paid' => 10000,
            'payment_method' => 'transfer',
        ])->assertCreated();

        $this->assertDatabaseHas('sales', ['payment_method' => 'transfer']);
    }

    public function test_cashier_history_only_shows_the_signed_in_cashiers_transactions(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $otherCashier = User::factory()->create(['role' => 'kasir']);

        $ownSale = Sale::query()->create([
            'invoice' => 'INV-OWN-001',
            'cashier_id' => $cashier->id,
            'cashier_name' => $cashier->name,
            'customer_type' => 'Umum',
            'subtotal' => 1000,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'total' => 1000,
            'paid' => 1000,
            'change' => 0,
            'payment_method' => 'tunai',
        ]);
        Sale::query()->create([
            'invoice' => 'INV-OTHER-001',
            'cashier_id' => $otherCashier->id,
            'cashier_name' => $otherCashier->name,
            'customer_type' => 'Umum',
            'subtotal' => 2000,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'total' => 2000,
            'paid' => 2000,
            'change' => 0,
            'payment_method' => 'tunai',
        ]);

        $this->actingAs($cashier)->get(route('cashier.riwayat'))
            ->assertOk()
            ->assertSee('INV-OWN-001')
            ->assertSee(route('cashier.receipt.print', $ownSale), false)
            ->assertSee('Cetak struk')
            ->assertDontSee('INV-OTHER-001');
    }

    public function test_cashier_cannot_print_another_cashiers_receipt(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $otherCashier = User::factory()->create(['role' => 'kasir']);
        $sale = Sale::query()->create([
            'invoice' => 'INV-OTHER-002',
            'cashier_id' => $otherCashier->id,
            'cashier_name' => $otherCashier->name,
            'customer_type' => 'Umum',
            'subtotal' => 2000,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'total' => 2000,
            'paid' => 2000,
            'change' => 0,
            'payment_method' => 'tunai',
        ]);

        $this->actingAs($cashier)
            ->post(route('cashier.receipt.print', $sale))
            ->assertNotFound();

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->post(route('cashier.receipt.print', $sale))
            ->assertForbidden();
    }

    public function test_cashier_can_print_their_own_receipt(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $sale = Sale::query()->create([
            'invoice' => 'INV-PRINT-001',
            'cashier_id' => $cashier->id,
            'cashier_name' => $cashier->name,
            'customer_type' => 'Umum',
            'subtotal' => 3000,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'total' => 3000,
            'paid' => 5000,
            'change' => 2000,
            'payment_method' => 'tunai',
        ]);
        $sale->items()->create([
            'product_name' => 'Air Mineral',
            'price' => 3000,
            'quantity' => 1,
            'subtotal' => 3000,
        ]);

        $printer = new class implements ReceiptPrinter
        {
            public ?string $printedInvoice = null;

            public function print(Sale $sale, StoreSetting $store): void
            {
                $this->printedInvoice = $sale->invoice;
            }
        };
        $this->app->instance(ReceiptPrinter::class, $printer);

        $this->actingAs($cashier)
            ->from(route('cashier.riwayat'))
            ->post(route('cashier.receipt.print', $sale))
            ->assertRedirect(route('cashier.riwayat'))
            ->assertSessionHas('status', 'Struk transaksi INV-PRINT-001 berhasil dicetak.');

        $this->assertSame('INV-PRINT-001', $printer->printedInvoice);
    }

    public function test_cashier_can_search_history_by_invoice_contact_and_product_name(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $otherCashier = User::factory()->create(['role' => 'kasir']);

        $sale = Sale::query()->create([
            'invoice' => 'INV-KOPI-001',
            'cashier_id' => $cashier->id,
            'cashier_name' => $cashier->name,
            'customer_type' => 'Pelanggan Member',
            'customer_contact' => '08123456789',
            'subtotal' => 15000,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'total' => 15000,
            'paid' => 15000,
            'change' => 0,
            'payment_method' => 'tunai',
        ]);
        $sale->items()->create([
            'product_name' => 'Kopi Jazzel',
            'price' => 15000,
            'quantity' => 1,
            'subtotal' => 15000,
        ]);

        Sale::query()->create([
            'invoice' => 'INV-OTHER-001',
            'cashier_id' => $otherCashier->id,
            'cashier_name' => $otherCashier->name,
            'customer_type' => 'Umum',
            'subtotal' => 2000,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'total' => 2000,
            'paid' => 2000,
            'change' => 0,
            'payment_method' => 'qris',
        ]);

        foreach (['KOPI', '08123456789', 'Jazzel'] as $search) {
            $this->actingAs($cashier)
                ->get(route('cashier.riwayat', ['search' => $search]))
                ->assertOk()
                ->assertSee('INV-KOPI-001')
                ->assertDontSee('INV-OTHER-001');
        }
    }

    public function test_cashier_history_can_be_filtered_by_day_week_month_and_year(): void
    {
        $this->travelTo(Carbon::parse('2026-10-05 12:00:00'));
        $cashier = User::factory()->create(['role' => 'kasir']);

        foreach ([
            ['INV-TODAY', now()],
            ['INV-THIS-WEEK', Carbon::parse('2026-10-06 12:00:00')],
            ['INV-THIS-MONTH', Carbon::parse('2026-10-02 12:00:00')],
            ['INV-THIS-YEAR', Carbon::parse('2026-01-01 12:00:00')],
            ['INV-LAST-YEAR', Carbon::parse('2025-12-31 12:00:00')],
        ] as [$invoice, $createdAt]) {
            $sale = Sale::query()->create([
                'invoice' => $invoice,
                'cashier_id' => $cashier->id,
                'cashier_name' => $cashier->name,
                'customer_type' => 'Umum',
                'subtotal' => 1000,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'tax' => 0,
                'other_fee' => 0,
                'total' => 1000,
                'paid' => 1000,
                'change' => 0,
                'payment_method' => 'tunai',
            ]);
            $sale->forceFill(['created_at' => $createdAt])->save();
        }

        $expectedInvoices = [
            'day' => ['INV-TODAY'],
            'week' => ['INV-TODAY', 'INV-THIS-WEEK'],
            'month' => ['INV-TODAY', 'INV-THIS-WEEK', 'INV-THIS-MONTH'],
            'year' => ['INV-TODAY', 'INV-THIS-WEEK', 'INV-THIS-MONTH', 'INV-THIS-YEAR'],
            'all' => ['INV-TODAY', 'INV-THIS-WEEK', 'INV-THIS-MONTH', 'INV-THIS-YEAR', 'INV-LAST-YEAR'],
        ];

        foreach ($expectedInvoices as $period => $invoices) {
            $response = $this->actingAs($cashier)
                ->get(route('cashier.riwayat', ['period' => $period]))
                ->assertOk();

            foreach ($invoices as $invoice) {
                $response->assertSee($invoice);
            }

            foreach (array_diff(array_column([
                ['INV-TODAY'],
                ['INV-THIS-WEEK'],
                ['INV-THIS-MONTH'],
                ['INV-THIS-YEAR'],
                ['INV-LAST-YEAR'],
            ], 0), $invoices) as $invoice) {
                $response->assertDontSee($invoice);
            }
        }
    }

    public function test_admin_sales_report_uses_saved_transactions_from_all_cashiers(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cashier = User::factory()->create(['role' => 'kasir', 'name' => 'Kasir Satu']);
        $otherCashier = User::factory()->create(['role' => 'kasir', 'name' => 'Kasir Dua']);

        Sale::query()->create([
            'invoice' => 'INV-ADMIN-REPORT-001',
            'cashier_id' => $cashier->id,
            'cashier_name' => $cashier->name,
            'customer_type' => 'Umum',
            'subtotal' => 25000,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'total' => 25000,
            'paid' => 30000,
            'change' => 5000,
            'payment_method' => 'tunai',
            'created_at' => now(),
        ]);
        $historicalSale = Sale::query()->create([
            'invoice' => 'INV-ADMIN-REPORT-002',
            'cashier_id' => $otherCashier->id,
            'cashier_name' => $otherCashier->name,
            'customer_type' => 'Pelanggan Member',
            'subtotal' => 15000,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'total' => 15000,
            'paid' => 15000,
            'change' => 0,
            'payment_method' => 'qris',
        ]);
        $historicalSale->forceFill(['created_at' => now()->subDay()])->save();

        $this->actingAs($admin)->get(route('admin.laporan'))
            ->assertOk()
            ->assertSee('Rp 25.000')
            ->assertSee('INV-ADMIN-REPORT-001')
            ->assertSee('Kasir Satu')
            ->assertSee('TUNAI')
            ->assertSee('INV-ADMIN-REPORT-002')
            ->assertSee('Kasir Dua')
            ->assertSee('QRIS')
            ->assertSee('2 transaksi tersimpan');
    }

    public function test_admin_dashboard_graph_shows_seven_days_of_saved_sales_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cashier = User::factory()->create(['role' => 'kasir', 'name' => 'Kasir Grafik']);

        foreach ([
            ['INV-CHART-TODAY-1', 25000, today()->setTime(9, 0)],
            ['INV-CHART-TODAY-2', 10000, today()->setTime(15, 0)],
            ['INV-CHART-WEEK', 9000, today()->subDays(6)->setTime(12, 0)],
            ['INV-CHART-OLD', 70000, today()->subDays(8)->setTime(12, 0)],
        ] as [$invoice, $total, $createdAt]) {
            $sale = Sale::query()->create([
                'invoice' => $invoice,
                'cashier_id' => $cashier->id,
                'cashier_name' => $cashier->name,
                'customer_type' => 'Umum',
                'subtotal' => $total,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'tax' => 0,
                'other_fee' => 0,
                'total' => $total,
                'paid' => $total,
                'change' => 0,
                'payment_method' => 'tunai',
            ]);
            $sale->forceFill(['created_at' => $createdAt])->save();
        }

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Grafik penjualan tujuh hari terakhir')
            ->assertSee('7 hari terakhir · Rp 44.000')
            ->assertSee(today()->format('d/m').': Rp 35.000')
            ->assertDontSee('Rp 114.000');
    }
}
