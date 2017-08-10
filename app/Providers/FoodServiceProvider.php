<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FoodServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind("food", function($app){
            return new \App\Services\FoodService("rice");
        });

//        $food = new \App\Services\FoodService("noodle");
//        $this->app->instance("App\Service\FoodService", $food);

        $food = new \App\Services\FoodService("呷哺呷哺 + 旋转火锅");
//        $this->app->bind("App\Services\FoodService", function($app) use ($food){
//            return $food;
//        });

        $this->app->instance("App\Services\FoodService", $food);
    }
}
