<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CashierController;
use App\Http\Controllers\Admin\StoreSettingController;
use App\Http\Controllers\Cashier\SaleController;
use App\Http\Controllers\StrukController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware(['auth', 'verified', 'role:kasir'])->group(function () {
    Route::post('/kasir/transaksi', [SaleController::class, 'store'])->name('cashier.checkout');
    Route::post('/kasir/riwayat/{sale}/struk', [StrukController::class, 'printReceipt'])->name('cashier.receipt.print');
    Route::get('/kasir/riwayat', [SaleController::class, 'index'])->name('cashier.riwayat');

    Route::get('/kasir', function () {
        return view('cashier.index', [
            'products' => Product::query()->where('is_active', true)->where('stock', '>', 0)->orderBy('name')->get(),
        ]);
    })->name('cashier.index');

    Route::get('/kasir/transaksi', function () {
        return view('cashier.index', [
            'products' => Product::query()->where('is_active', true)->where('stock', '>', 0)->orderBy('name')->get(),
        ]);
    })->name('cashier.transaksi');

    Route::get('/kasir/produk', function (Request $request) {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);
        $search = trim($filters['search'] ?? '');

        $products = Product::query()
            ->where('is_active', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();

        return view('cashier.produk', [
            'products' => $products,
            'search' => $search,
        ]);
    })->name('cashier.produk');

    Route::get('/kasir/laporan', function (Request $request) {
        $cashierId = $request->user()->id;
        $currentYear = now()->year;
        $firstSaleDate = Sale::query()
            ->where('cashier_id', $cashierId)
            ->oldest('created_at')
            ->value('created_at');
        $firstYear = $firstSaleDate ? Carbon::parse($firstSaleDate)->year : $currentYear;
        $years = collect(range(min($firstYear, $currentYear), $currentYear))
            ->reverse()
            ->values();

        $filters = $request->validate([
            'period' => ['nullable', 'in:day,week,month'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'week' => ['nullable', 'integer', 'between:1,53'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:1900,'.$currentYear],
        ]);

        $period = $filters['period'] ?? 'day';
        $selectedDate = $filters['date'] ?? today()->toDateString();
        $selectedMonth = (int) ($filters['month'] ?? now()->month);
        $selectedYear = (int) ($filters['year'] ?? $currentYear);
        $selectedWeek = (int) ($filters['week'] ?? now()->format('W'));
        $start = match ($period) {
            'week' => Carbon::now()->setISODate($selectedYear, $selectedWeek, 1)->startOfDay(),
            'month' => Carbon::create($selectedYear, $selectedMonth, 1)->startOfDay(),
            default => Carbon::parse($selectedDate)->startOfDay(),
        };
        $end = match ($period) {
            'week' => $start->copy()->endOfWeek(Carbon::SUNDAY),
            'month' => $start->copy()->endOfMonth()->endOfDay(),
            default => $start->copy()->endOfDay(),
        };

        if ($period === 'week' && (int) $start->format('o') !== $selectedYear) {
            throw ValidationException::withMessages([
                'week' => 'Minggu yang dipilih tidak tersedia pada tahun tersebut.',
            ]);
        }

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $weekOptions = [];
        for ($weekNumber = 1; $weekNumber <= 53; $weekNumber++) {
            $weekStart = Carbon::now()->setISODate($selectedYear, $weekNumber, 1)->startOfDay();
            if ((int) $weekStart->format('o') !== $selectedYear) {
                break;
            }

            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
            $weekOptions[$weekNumber] = sprintf(
                'Minggu ke-%d (%s–%s)',
                $weekNumber,
                $weekStart->format('d/m'),
                $weekEnd->format('d/m/Y'),
            );
        }

        if ($period === 'week' && ! array_key_exists($selectedWeek, $weekOptions)) {
            throw ValidationException::withMessages([
                'week' => 'Minggu yang dipilih tidak tersedia pada tahun tersebut.',
            ]);
        }

        $cashierSales = Sale::query()->where('cashier_id', $cashierId);
        $filteredSales = (clone $cashierSales)->whereBetween('created_at', [$start, $end]);
        $periodSales = (clone $filteredSales)->sum('total');
        $transactionCount = (clone $filteredSales)->count();
        $periodLabel = match ($period) {
            'week' => $weekOptions[$selectedWeek],
            'month' => $monthNames[$selectedMonth].' '.$selectedYear,
            default => $start->format('d').' '.$monthNames[(int) $start->format('n')].' '.$start->format('Y'),
        };

        return view('cashier.laporan', [
            'totalSales' => (clone $cashierSales)->whereDate('created_at', today())->sum('total'),
            'monthlySales' => (clone $cashierSales)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total'),
            'lifetimeSales' => (clone $cashierSales)->sum('total'),
            'periodSales' => $periodSales,
            'transactionCount' => $transactionCount,
            'productsCount' => Product::query()->where('is_active', true)->count(),
            'transactions' => $filteredSales->latest()->paginate(25)->withQueryString(),
            'period' => $period,
            'selectedDate' => $selectedDate,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'selectedWeek' => $selectedWeek,
            'years' => $years,
            'monthNames' => $monthNames,
            'weekOptions' => $weekOptions,
            'periodLabel' => $periodLabel,
        ]);
    })->name('cashier.laporan');
});

Route::get('/dashboard', function () {
    $products = Product::latest()->get();

    return view('dashboard', [
        'products'         => $products,
        'totalProducts'    => $products->count(),
        'activeProducts'   => $products->where('is_active', true)->count(),
        'lowStockProducts' => $products->where('stock', '<=', 5)->count(),
        'inventoryValue'   => $products->sum(fn (Product $product) => $product->price * $product->stock),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::resource('products', ProductController::class);

    Route::get('/admin/pengaturan', [StoreSettingController::class, 'edit'])->name('admin.pengaturan');
    Route::put('/admin/pengaturan/toko', [StoreSettingController::class, 'updateStore'])->name('admin.pengaturan.toko');
    Route::put('/admin/pengaturan/akun', [StoreSettingController::class, 'updateAccount'])->name('admin.pengaturan.akun');
    Route::post('/admin/kasir', [CashierController::class, 'store'])->name('admin.kasir.store');

    Route::get('/admin', function () {
        $allProducts = Product::query()->get();
        $chartStart = today()->subDays(6);
        $dailySales = Sale::query()
            ->whereBetween('created_at', [$chartStart, today()->endOfDay()])
            ->get(['created_at', 'total'])
            ->groupBy(fn (Sale $sale) => $sale->created_at->toDateString())
            ->map(fn ($sales) => (float) $sales->sum('total'));
        $salesChart = collect(range(0, 6))->map(function (int $day) use ($chartStart, $dailySales) {
            $date = $chartStart->copy()->addDays($day);

            return [
                'label' => $date->format('d/m'),
                'total' => $dailySales->get($date->toDateString(), 0),
            ];
        });

        return view('admin', [
            'products' => Product::latest()->limit(5)->get(),
            'cashiers' => User::where('role', 'kasir')->get(),
            'salesChart' => $salesChart,
            'totalProducts' => $allProducts->count(),
            'activeProducts' => $allProducts->where('is_active', true)->count(),
            'lowStockProducts' => $allProducts->where('stock', '<=', 5)->count(),
            'inventoryValue' => $allProducts->sum(fn (Product $product) => $product->price * $product->stock),
        ]);
    })->name('admin.dashboard');

    Route::get('/admin/barang', function () {
        return view('admin.barang', [
            'products' => Product::latest()->get(),
        ]);
    })->name('admin.barang');

    Route::get('/admin/kasir', function () {
        return view('admin.kasir', [
            'cashiers' => User::whereIn('role', ['admin', 'kasir'])->get(),
        ]);
    })->name('admin.kasir');

    Route::get('/admin/laporan', function (Request $request) {
        $currentYear = now()->year;
        $firstSaleDate = Sale::query()->oldest('created_at')->value('created_at');
        $firstYear = $firstSaleDate ? Carbon::parse($firstSaleDate)->year : $currentYear;
        $years = collect(range(min($firstYear, $currentYear), $currentYear))
            ->reverse()
            ->values();

        $filters = $request->validate([
            'period' => ['nullable', 'in:day,week,month'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'week' => ['nullable', 'integer', 'between:1,53'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:1900,'.$currentYear],
        ]);

        $period = $filters['period'] ?? 'day';
        $selectedDate = $filters['date'] ?? today()->toDateString();
        $selectedMonth = (int) ($filters['month'] ?? now()->month);
        $selectedYear = (int) ($filters['year'] ?? $currentYear);
        $selectedWeek = (int) ($filters['week'] ?? now()->format('W'));
        $start = match ($period) {
            'week' => Carbon::now()->setISODate($selectedYear, $selectedWeek, 1)->startOfDay(),
            'month' => Carbon::create($selectedYear, $selectedMonth, 1)->startOfDay(),
            default => Carbon::parse($selectedDate)->startOfDay(),
        };
        $end = match ($period) {
            'week' => $start->copy()->endOfWeek(Carbon::SUNDAY),
            'month' => $start->copy()->endOfMonth()->endOfDay(),
            default => $start->copy()->endOfDay(),
        };

        if ($period === 'week' && (int) $start->format('o') !== $selectedYear) {
            throw ValidationException::withMessages([
                'week' => 'Minggu yang dipilih tidak tersedia pada tahun tersebut.',
            ]);
        }

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $weekOptions = [];
        for ($weekNumber = 1; $weekNumber <= 53; $weekNumber++) {
            $weekStart = Carbon::now()->setISODate($selectedYear, $weekNumber, 1)->startOfDay();
            if ((int) $weekStart->format('o') !== $selectedYear) {
                break;
            }

            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
            $weekOptions[$weekNumber] = sprintf(
                'Minggu ke-%d (%s–%s)',
                $weekNumber,
                $weekStart->format('d/m'),
                $weekEnd->format('d/m/Y'),
            );
        }

        if ($period === 'week' && ! array_key_exists($selectedWeek, $weekOptions)) {
            throw ValidationException::withMessages([
                'week' => 'Minggu yang dipilih tidak tersedia pada tahun tersebut.',
            ]);
        }

        $filteredSales = Sale::query()->whereBetween('created_at', [$start, $end]);
        $periodSales = (clone $filteredSales)->sum('total');
        $transactionCount = (clone $filteredSales)->count();
        $periodLabel = match ($period) {
            'week' => $weekOptions[$selectedWeek],
            'month' => $monthNames[$selectedMonth].' '.$selectedYear,
            default => $start->format('d').' '.$monthNames[(int) $start->format('n')].' '.$start->format('Y'),
        };

        return view('admin.laporan', [
            'totalSales' => Sale::query()->whereDate('created_at', today())->sum('total'),
            'monthlySales' => Sale::query()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total'),
            'lifetimeSales' => Sale::query()->sum('total'),
            'periodSales' => $periodSales,
            'transactionCount' => $transactionCount,
            'productsCount' => Product::query()->where('is_active', true)->count(),
            'transactions' => $filteredSales->latest()->paginate(25)->withQueryString(),
            'period' => $period,
            'selectedDate' => $selectedDate,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'selectedWeek' => $selectedWeek,
            'years' => $years,
            'monthNames' => $monthNames,
            'weekOptions' => $weekOptions,
            'periodLabel' => $periodLabel,
        ]);
    })->name('admin.laporan');
});

// TAMBAHKAN ROUTE TUNGGAL LAINNYA DI SINI JIKA ADA

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // TAMBAHKAN ROUTE BARU DALAM GRUP DI SINI
});

require __DIR__.'/auth.php';