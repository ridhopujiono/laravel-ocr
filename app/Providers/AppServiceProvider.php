<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('*', function () {
            if (!Session::has('locale')) {
                $browserLang = substr(request()->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
                $default = in_array($browserLang, ['id', 'en']) ? $browserLang : 'id';
                Session::put('locale', $default);
            }

            App::setLocale(Session::get('locale', config('app.locale')));
        });

    }
}
