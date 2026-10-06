<?php

namespace App\Providers;

use App\Contracts\ReceiptPrinter;
use App\Models\StoreSetting;
use App\Services\WindowsEscPosReceiptPrinter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ReceiptPrinter::class, WindowsEscPosReceiptPrinter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.admin', 'auth.login', 'dashboard', 'admin'], function ($view): void {
            $view->with('storeSettings', StoreSetting::current());
        });
    }
}
