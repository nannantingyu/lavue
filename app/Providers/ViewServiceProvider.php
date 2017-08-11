<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->share('app_name', 'lavue');
        view()->share('base_url', 'http://lav.com/');
        view()->share('default_seo_keywords', "五常大米,稻花香,大米,粮叔叔,粗粮,农产品");
        view()->share('default_seo_title', "米中贵族_五常大米_中国最好的大米_香米_米香四溢_柴米油盐-粮叔叔");
        view()->share('default_seo_description', "粮叔叔提供最新最全面的农业信息，健康信息");
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
