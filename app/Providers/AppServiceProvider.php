<?php

namespace App\Providers;

use App\Extensions\Config\Repository;
use App\Kd9703MediaBinder;
use App\Providers\UserProviders\UserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Kd9703\Constants\Media;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app(Kd9703MediaBinder::class)->bind(Media::TWITTER());
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
