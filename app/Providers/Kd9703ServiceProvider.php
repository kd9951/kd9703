<?php

namespace App\Providers;

use App\Kd9703MediaBinder;
use Illuminate\Support\ServiceProvider;

class Kd9703ServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\Kd9703\MediaBinder::class, Kd9703MediaBinder::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->make(\Kd9703\MediaBinder::class)->bind(null);
    }
}
