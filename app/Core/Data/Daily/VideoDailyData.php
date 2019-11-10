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
        $today = Carbon::now();
        $day = $today->day;

        $dailyTracking = VideoDailyTracker::where('video_id', $this->id)->first();

        $todayData = $dailyTracking->{'day' . $day};

        if($day === 1) {
            $lastDayOfPreviousMonth = $today->startOfMonth()->subSeconds(1)->day;
            $yesterdayData = $dailyTracking->{'day' . $lastDayOfPreviousMonth};
        } else {
            $yesterdayData = $dailyTracking->{'day' . ($day - 1)};
        }

        return [
            'yesterday' => [
                'views' => ($yesterdayData['views']) ? $yesterdayData['views'] : $todayData['views'],
                'earned' => ($yesterdayData['earned']) ? $yesterdayData['earned'] : $todayData['earned']
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
