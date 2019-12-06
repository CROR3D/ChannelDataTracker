<?php

namespace App\Core\Data\Daily;

use Carbon\Carbon;
use App\Core\Data\Daily\DailyData;
use App\Models\VideoDailyTracker;
use App\Models\History;

class VideoDailyData extends DailyData
{
    public function __construct($id)
    {
        parent::__construct($id);
    }

    public function get()
    {
        $getToday = Carbon::now()->day;

        $dailyTracking = $this->getMonthData();

        $todayData = $dailyTracking->{'day' . $getToday};

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
                'views' => ($yesterdayData['views']) ? $yesterdayData['views'] : 0,
                'earned' => ($yesterdayData['earned']) ? $yesterdayData['earned'] : 0
            ],
            'today' => [
                'views' => $todayData['views'],
                'earned' => $todayData['earned']
            ]
        ];
    }

    public function getMonthData()
    {
        return VideoDailyTracker::where('video_id', $this->id)->first();
    }

    public function getYearData()
    {
        return History::where('video_id', $this->id)->first();
    }
}
