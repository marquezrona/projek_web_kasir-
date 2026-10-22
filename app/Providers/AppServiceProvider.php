<?php

namespace App\Providers;

use App\Contracts\ReceiptPrinter;
use App\Models\StoreSetting;
use App\Services\WindowsEscPosReceiptPrinter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ReceiptPrinter::class, WindowsEscPosReceiptPrinter::class);
    }

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        View::composer(['layouts.admin', 'auth.login', 'dashboard', 'admin'], function ($view): void {
            $view->with('storeSettings', StoreSetting::current());
        });
    }
}
