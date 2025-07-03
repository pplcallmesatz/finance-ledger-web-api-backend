<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Removed HTTPS forcing for development
        // $this->app['request']->server->set('HTTPS', 'on');
        // \URL::forceScheme('https');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // $user = \App\Models\User::find(1);
        // \Illuminate\Support\Facades\Auth::login($user);
        
    }
}
