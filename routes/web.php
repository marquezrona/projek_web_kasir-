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

    Route::get('/kasir/produk', function () {
        return view('cashier.produk', [
            'products' => Product::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    })->name('cashier.produk');

    Route::get('/kasir/laporan', function () {
        return view('cashier.laporan', [
            'products' => Product::query()->where('is_active', true)->orderBy('name')->limit(6)->get(),
            'totalSales' => 0,
            'transactions' => [
                ['invoice' => '#INV-101', 'customer' => 'Umum', 'total' => 25000, 'status' => 'Lunas'],
                ['invoice' => '#INV-102', 'customer' => 'Member', 'total' => 47000, 'status' => 'Lunas'],
            ],
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

    Route::get('/admin/laporan', function () {
        return view('admin.laporan', [
            'totalProducts' => Product::count(),
            'totalSales' => Sale::query()->whereDate('created_at', today())->sum('total'),
            'todayTransactionCount' => Sale::query()->whereDate('created_at', today())->count(),
            'transactions' => Sale::query()->latest()->paginate(10),
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