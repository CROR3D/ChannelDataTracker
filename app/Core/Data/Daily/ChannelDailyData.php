<?php

namespace App\Core\Data\Daily;

use Carbon\Carbon;
use App\Core\Data\Daily\DailyData;
use App\Models\ChannelDailyTracker;

class ChannelDailyData extends DailyData
{
    public function __construct($id, $userId)
    {
        parent::__construct($id, $userId);
    }

    public function get()
    {
        $getToday = Carbon::now()->day;

        $dailyTracking = $this->getMonthData();

        if($getToday === 1)
        {
            $getYesterday = Carbon::now()->startOfMonth()->subSeconds(1)->day;
        }
        else
        {
            $getYesterday = $getToday - 1;
        }
        
        $todayData = $dailyTracking->{'day' . $getToday};
        $yesterdayData = $dailyTracking->{'day' . $getYesterday};

        return [
            'yesterday' => [
                'currentViews' => $yesterdayData['currentViews'],
                'currentSubs' => $yesterdayData['currentSubs'],
                'views' => ($yesterdayData['views']) ? $yesterdayData['views'] : 0,
                'subs' => ($yesterdayData['subs']) ? $yesterdayData['subs'] : 0
            ],
            'today' => [
                'currentViews' => $todayData['currentViews'],
                'currentSubs' => $todayData['currentSubs'],
                'views' => $todayData['views'],
                'subs' => $todayData['subs']
            ]
        ];
    }

    public function getMonthData()
    {
        return ChannelDailyTracker::where('channel_id', $this->id)->where('user_id', $this->userId)->first();
    }
}
