<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::resource('products', ProductController::class);

Route::get('/', function () {
    return view('welcome');
});

// Route baru untuk menampilkan halaman login
Route::get('/login', function () {
    return view('login');
})->name('login');

// Route Admin Dashboard
Route::get('/admin', function () {
    return view('admin');
})->name('admin.dashboard');

Route::resource('products', ProductController::class);
Route::get('/', function () {
    return view('welcome');
});
