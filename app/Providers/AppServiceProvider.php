<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Session;
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
        // Register a component alias for app-layout
        Blade::component('components.layouts.app', 'app-layout');
        
        // Set the application locale based on the session
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }
    }
}
