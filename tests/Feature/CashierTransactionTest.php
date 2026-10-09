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
        $this->assertSame('Umum', $sale->customer_type);
        $this->assertNull($sale->customer_contact);
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

    public function test_cashier_can_print_receipt_via_json_request(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $sale = Sale::query()->create([
            'invoice' => 'INV-PRINT-JSON',
            'cashier_id' => $cashier->id,
            'cashier_name' => $cashier->name,
            'customer_type' => 'Umum',
            'subtotal' => 5000,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'total' => 5000,
            'paid' => 5000,
            'change' => 0,
            'payment_method' => 'tunai',
        ]);
        $sale->items()->create([
            'product_name' => 'Biskuit',
            'price' => 5000,
            'quantity' => 1,
            'subtotal' => 5000,
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
            ->postJson(route('cashier.receipt.print', $sale))
            ->assertOk()
            ->assertJson([
                'message' => 'Struk transaksi INV-PRINT-JSON berhasil dicetak.',
            ]);

        $this->assertSame('INV-PRINT-JSON', $printer->printedInvoice);
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

    public function test_cashier_sales_report_filters_by_named_month_and_shows_month_and_lifetime_totals(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $otherCashier = User::factory()->create(['role' => 'kasir']);
        $monthNames = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];
        $this->createReportSale($cashier, 'INV-REPORT-CURRENT', 30000, now());
        $this->createReportSale($cashier, 'INV-REPORT-OLDER', 20000, now()->subMonth()->startOfMonth()->addDays(2));
        $this->createReportSale($otherCashier, 'INV-REPORT-OTHER', 90000, now());

        $this->actingAs($cashier)
            ->get(route('cashier.laporan', [
                'period' => 'month',
                'month' => now()->month,
                'year' => now()->year,
            ]))
            ->assertOk()
            ->assertSee('Penjualan Bulan Ini')
            ->assertSee('Penjualan Selama Ini')
            ->assertSee('Rp 30.000')
            ->assertSee('Rp 50.000')
            ->assertSee($monthNames[now()->month - 1].' '.now()->year)
            ->assertSee('INV-REPORT-CURRENT')
            ->assertDontSee('INV-REPORT-OLDER')
            ->assertDontSee('INV-REPORT-OTHER');
    }

    public function test_cashier_sales_report_filters_by_selected_iso_week(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $otherCashier = User::factory()->create(['role' => 'kasir']);
        $weekStart = now()->startOfWeek(Carbon::MONDAY);
        $this->createReportSale($cashier, 'INV-WEEK-FIRST', 12000, $weekStart->copy()->addHours(10));
        $this->createReportSale($cashier, 'INV-WEEK-LAST', 18000, $weekStart->copy()->addDays(5)->addHours(12));
        $this->createReportSale($cashier, 'INV-WEEK-OLDER', 25000, $weekStart->copy()->subDay());
        $this->createReportSale($otherCashier, 'INV-WEEK-OTHER', 35000, $weekStart->copy()->addDays(2));

        $this->actingAs($cashier)
            ->get(route('cashier.laporan', [
                'period' => 'week',
                'week' => (int) now()->format('W'),
                'year' => (int) now()->format('o'),
            ]))
            ->assertOk()
            ->assertSee('Minggu ke-'.now()->format('W'))
            ->assertSee('INV-WEEK-FIRST')
            ->assertSee('INV-WEEK-LAST')
            ->assertDontSee('INV-WEEK-OLDER')
            ->assertDontSee('INV-WEEK-OTHER');
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
        $this->travelTo(Carbon::parse('2026-10-09 12:00:00'));
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

        $this->actingAs($admin)->get(route('admin.laporan', [
            'period' => 'month',
            'month' => now()->month,
            'year' => now()->year,
        ]))
            ->assertOk()
            ->assertSee('Rp 25.000')
            ->assertSee('Rp 40.000')
            ->assertSee('Penjualan Selama Ini')
            ->assertSee('INV-ADMIN-REPORT-001')
            ->assertSee('Kasir Satu')
            ->assertSee('TUNAI')
            ->assertSee('INV-ADMIN-REPORT-002')
            ->assertSee('Kasir Dua')
            ->assertSee('QRIS')
            ->assertSee('2 transaksi');
    }

    public function test_admin_sales_report_filters_all_cashiers_sales_by_day_week_and_month(): void
    {
        $this->travelTo(Carbon::parse('2026-10-09 12:00:00'));
        $admin = User::factory()->create(['role' => 'admin']);
        $cashier = User::factory()->create(['role' => 'kasir', 'name' => 'Kasir Satu']);
        $otherCashier = User::factory()->create(['role' => 'kasir', 'name' => 'Kasir Dua']);
        $this->createReportSale($cashier, 'INV-ADMIN-DAY', 10000, now());
        $this->createReportSale($otherCashier, 'INV-ADMIN-WEEK', 20000, now()->subDays(2));
        $this->createReportSale($cashier, 'INV-ADMIN-MONTH', 30000, now()->subDays(5));
        $this->createReportSale($otherCashier, 'INV-ADMIN-OLDER', 40000, now()->subDays(9));

        $this->actingAs($admin)
            ->get(route('admin.laporan', ['period' => 'day', 'date' => today()->toDateString()]))
            ->assertOk()
            ->assertSee('INV-ADMIN-DAY')
            ->assertDontSee('INV-ADMIN-WEEK')
            ->assertDontSee('INV-ADMIN-MONTH')
            ->assertDontSee('INV-ADMIN-OLDER')
            ->assertSee('1 transaksi');

        $this->actingAs($admin)
            ->get(route('admin.laporan', [
                'period' => 'week',
                'week' => (int) now()->format('W'),
                'year' => (int) now()->format('o'),
            ]))
            ->assertOk()
            ->assertSee('INV-ADMIN-DAY')
            ->assertSee('INV-ADMIN-WEEK')
            ->assertDontSee('INV-ADMIN-MONTH')
            ->assertDontSee('INV-ADMIN-OLDER')
            ->assertSee('Rp 30.000');

        $this->actingAs($admin)
            ->get(route('admin.laporan', [
                'period' => 'month',
                'month' => now()->month,
                'year' => now()->year,
            ]))
            ->assertOk()
            ->assertSee('INV-ADMIN-DAY')
            ->assertSee('INV-ADMIN-WEEK')
            ->assertSee('INV-ADMIN-MONTH')
            ->assertDontSee('INV-ADMIN-OLDER')
            ->assertSee('Rp 60.000')
            ->assertSee('Rp 100.000');
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

    private function createReportSale(User $cashier, string $invoice, int $total, Carbon $createdAt): Sale
    {
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

        $sale->forceFill([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ])->save();

        return $sale;
    }
}
