<?php

namespace App\Core\Data\Daily;

use Carbon\Carbon;
use App\Models\ChannelDailyTracker;

abstract class DailyData
{
    protected $id;
    protected $userId;

    public function __construct($id, $userId)
    {
        $this->id = $id;
        $this->userId = $userId;
    }

    public abstract function get();
}
