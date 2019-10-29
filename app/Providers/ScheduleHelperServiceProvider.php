<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ScheduleHelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('schedulehelper', function() {
            return new \App\Helpers\ScheduleHelper;
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
