<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ScheduleHelper extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'schedulehelper';
    }

}
