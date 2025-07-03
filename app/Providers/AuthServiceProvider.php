<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        'App\Models\User' => 'App\Policies\UserPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Automatically finding the Policies
        // Gate::guessPolicyNamesUsing(function ($modelClass) {
        //     return 'App\\Policies\\' . class_basename($modelClass) . 'Policy';
        // });
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });
        $this->registerPolicies();
    }
}
