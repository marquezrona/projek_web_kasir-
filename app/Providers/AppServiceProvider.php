<?php

namespace App\Providers;

use App\Models\StoreSetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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
