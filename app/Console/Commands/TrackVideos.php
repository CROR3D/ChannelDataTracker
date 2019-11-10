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
            $videoDailyTracker = VideoDailyTracker::where('video_id', $videoId)->first();
            $today = Carbon::now();
            $day = $today->day;

            if($day === 1) {
                $lastDayOfPreviousMonth = $today->startOfMonth()->subSeconds(1)->day;
            } else {
                $lastDayOfPreviousMonth = $day - 1;
            }

            $dataDay = 'day' . $lastDayOfPreviousMonth;

            $updateData = [
                $dataDay => [
                    'views' => $data->statistics->viewCount,
                    'earned' => 0
                ]
            ];

            $videoDailyTracker->updateVideoDailyTracker($updateData);
        }
    }
}
