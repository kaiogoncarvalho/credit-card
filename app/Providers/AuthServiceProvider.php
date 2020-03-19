<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use App\Enums\Scope;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    
        Passport::tokensCan(
            Scope::getScopes()
        );
    
        Passport::setDefaultScope(Scope::USER);
    
        Passport::tokensExpireIn(now()->addHours(4));
    
        Passport::refreshTokensExpireIn(now()->addHours(12));
    }
}
