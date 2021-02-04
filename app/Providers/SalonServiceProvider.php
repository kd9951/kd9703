<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kd9703\Usecases\Nishino\SetGlobalAccountRegulation as NishinoSetGlobalAccountRegulation;
use Kd9703\Usecases\Progress\SetGlobalAccountRegulation as ProgressSetGlobalAccountRegulation;
use Kd9703\Usecases\SetGlobalAccountRegulation;

class SalonServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        switch (config('app.salon')) {
            // 中田敦彦オンラインサロン
            case 'progress':
                $this->app->bind(SetGlobalAccountRegulation::class, ProgressSetGlobalAccountRegulation::class);
                break;

            // 西野亮廣エンタメ研究所
            case 'nishino':
                $this->app->bind(SetGlobalAccountRegulation::class, NishinoSetGlobalAccountRegulation::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
