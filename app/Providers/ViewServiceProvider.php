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
        view()->share('base_url', 'http://www.yjshare.cn/');
        view()->share('default_seo_keywords', "五常大米,稻花香,大米,粮叔叔,粗粮,农产品");
        view()->share('default_seo_title', "米中贵族_五常大米_中国最好的大米_五常_炜煜水稻种植合作社-粮叔叔");
        view()->share('default_seo_description', "炜煜合作社提供正宗自产自销的稻花香大米，如有需要，请联系18801292741");
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
