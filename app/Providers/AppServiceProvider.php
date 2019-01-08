<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\Kafka;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Kafka::class, function ($app) {
            return new Kafka();
        });
    }
}
