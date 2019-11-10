<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class APIManagerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('apimanager', function() {
            return new \App\Helpers\APIManager;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
