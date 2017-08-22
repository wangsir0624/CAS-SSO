<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Wangjian\Dingding\DingdingClient;

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
        $this->app->singleton(DingdingClient::class, function($app) {
           return new DingdingClient($app['config']['dingding']);
        });
    }
}
