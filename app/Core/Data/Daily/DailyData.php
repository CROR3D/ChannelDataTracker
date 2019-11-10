<?php

namespace App\Core\Data\Daily;

use Carbon\Carbon;
use App\Models\ChannelDailyTracker;

abstract class DailyData
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public abstract function get();
}
