<?php

namespace App\Providers;

use App\Channel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 整个框架启动时设置时间配置为中文
        Carbon::setLocale('zh');

        // View::share('channels', \App\Channel::all());
        View::composer('*', function ($view) {
            // $view->with('channels', \App\Channel::all());
            $channels = Cache::rememberForever('channels', function() {
                return Channel::all();
            });
            $view->with('channels', $channels);
        });

        Validator::extend('spamfree', 'App\Rules\SpamFree@passes');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // if ($this->app->isLocal()) {
        //     $this->register(\Barryvdh\Debugbar\ServiceProvider::class);
        // }
    }
}
