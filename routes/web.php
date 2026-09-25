<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Models\Product;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::resource('products', ProductController::class);

Route::get('/kasir', function () {
    return view('cashier.index', [
        'products' => Product::query()->where('is_active', true)->where('stock', '>', 0)->orderBy('name')->get(),
    ]);
})->middleware(['auth', 'verified'])->name('cashier.index');

Route::get('/dashboard', function () {
    $products = Product::latest()->get();

    return view('dashboard', [
        'products' => $products,
        'totalProducts' => $products->count(),
        'activeProducts' => $products->where('is_active', true)->count(),
        'lowStockProducts' => $products->where('stock', '<=', 5)->count(),
        'inventoryValue' => $products->sum(fn (Product $product) => $product->price * $product->stock),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/admin', function () {
    return view('admin');
})->middleware(['auth', 'verified'])->name('admin.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';