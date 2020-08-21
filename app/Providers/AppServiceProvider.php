<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Carbon\Carbon::setLocale('zh-tw');

        Blade::directive('active', function ($expression) {
            return " (\$_menu == {$expression})? 'active' : ''";
        });

        Blade::directive('subdrop', function ($expression) {
            return "(in_array(\$_menu, {$expression}))? 'subdrop' : ''";
        });

        Blade::directive('removeoradd', function ($expression) {
            return "(in_array(\$_menu, {$expression}))? 'md-remove' : 'md-add'";
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
