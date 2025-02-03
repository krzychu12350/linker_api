<?php

namespace App\Providers;

use App\Rules\NotBanned;
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
    public function boot(): void
    {
        // Register the custom validation rule
        \Illuminate\Support\Facades\Validator::extend('not_banned', NotBanned::class);
    }
}
