<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;//se requiere para cambiar parametros de la contraseña
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
        ///con estos parametros se cambai el tamaño de la contraseña al registrarla
        Password::defaults(function () {
            return Password::min(4);
        });
    }
}
