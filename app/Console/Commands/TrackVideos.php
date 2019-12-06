<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use APIManager;
use App\Models\Video;
use App\Models\VideoDailyTracker;
use Carbon\Carbon;

class TrackVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:trackvideos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store video daily data in database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $videos = APIManager::getVideosData();

        foreach ($videos as $video) {
            $data = $video->items[0];
            $videoId = $data->id;
            $dbVideo = Video::find($videoId);
            $videoDailyTracker = VideoDailyTracker::where('video_id', $videoId)->first();
            $getToday = Carbon::now()->day;

            if($getToday === 1)
            {
                $getYesterday = Carbon::now()->startOfMonth()->subSeconds(1)->day;
            }
            else
            {
                $getYesterday = $getToday - 1;
            }

            $today = 'day' . $getToday;

            $yesterdayData = $videoDailyTracker->{'day' . $getYesterday};
            $currentViews = $data->statistics->viewCount;
            $dailyViews = $currentViews - $yesterdayData['currentViews'];

            $updateData = [
                $today => [
                    'currentViews' => $currentViews,
                    'views' => $dailyViews,
                    'earned' => ($dailyViews / 1000) * $dbVideo->earning_factor
                ]
            ];

            // TODO: IF UPDATE DATA IS THE SAME AS BEFORE (if returns false)
            $videoDailyTracker->updateVideoDailyTracker($updateData);
        }
    }
}
