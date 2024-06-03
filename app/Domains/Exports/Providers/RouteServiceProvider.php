<?php

namespace App\Domains\Exports\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->router->group([
            'prefix' => 'extract',
            'namespace' => 'App\Domains\Exports\Controllers',
        ], function ($router) {
            require app_path('Domains/Exports/routes/api.php');
        });
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
