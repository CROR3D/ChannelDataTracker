<?php

namespace App\Core\Data\Daily;

use Carbon\Carbon;
use App\Core\Data\Daily\DailyData;
use App\Models\ChannelDailyTracker;

class ChannelDailyData extends DailyData
{
    public function __construct($id)
    {
        parent::__construct($id);
    }

    public function get()
    {
        $today = Carbon::now();
        $day = $today->day;

        $dailyTracking = ChannelDailyTracker::where('channel_id', $this->id)->first();

        $todayData = $dailyTracking->{'day' . $day};

        if($day === 1) {
            $lastDayOfPreviousMonth = $today->startOfMonth()->subSeconds(1)->day;
            $yesterdayData = $dailyTracking->{'day' . $lastDayOfPreviousMonth};
        } else {
            $yesterdayData = $dailyTracking->{'day' . ($day - 1)};
        }

        return [
            'yesterday' => [
                'subs' => ($yesterdayData['subs']) ? $yesterdayData['subs'] : $todayData['subs'],
                'views' => ($yesterdayData['views']) ? $yesterdayData['views'] : $todayData['views'],
            ],
            'today' => [
                'subs' => $todayData['subs'],
                'views' => $todayData['views']
            ]
        ];
    }

    public function getMonthData()
    {
        return ChannelDailyTracker::where('channel_id', $this->id)->first();
    }
}
