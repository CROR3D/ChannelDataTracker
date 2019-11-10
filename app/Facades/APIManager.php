<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class APIManager extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'apimanager';
    }

}
