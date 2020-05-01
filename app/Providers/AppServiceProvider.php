<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // avoid issue with artican migrate on older MySQL installation: see https://laravel-news.com/laravel-5-4-key-too-long-error
        Schema::defaultStringLength(191);
    }
}
