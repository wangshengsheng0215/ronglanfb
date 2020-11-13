<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;
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
        //
        //扩展身份证验证规则
        Validator::extend('identitycards', function($attribute, $value, $parameters) {
            return preg_match('/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/', $value);
        });

    }
}
