<?php

namespace App\Providers;

use App\Extensions\Config\Repository;
use App\Providers\UserProviders\UserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->instance('config', new Repository($this->app->make('config')->all()));

        Auth::provider('user_provider', function ($app, array $config) {
            return $this->app->make(UserProvider::class);
        });
    }
}
