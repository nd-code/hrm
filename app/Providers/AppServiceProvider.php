<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

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
    public function boot()
    {
        // Global default format for Carbon when cast to string
        Carbon::setToStringFormat('d-m-Y h:i A'); 
        // Example: 18-09-2025 05:45 PM
    }
}
