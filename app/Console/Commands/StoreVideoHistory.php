<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Video;
use App\Models\VideoDailyTracker;
use App\Models\History;
use Carbon\Carbon;

class StoreVideoHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:storevideohistory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store video data of the previous month';

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
        $videos = Video::all();
        $today = Carbon::now();
        $lastMonth = lcfirst($today->subMonth()->format('F'));

        foreach ($videos as $video) {
            $videoMonthData = VideoDailyTracker::where('video_id', $video->id)->first();
            $videoMonthData = $videoMonthData->getAttributes();
            $videoMonthData = array_slice($videoMonthData, 1, -2);

            $totalViews = 0;
            $totalEarned = 0;

            foreach ($videoMonthData as $day) {
                $day = json_decode($day);
                if($day) {
                    $totalViews += $day->views;
                    $totalEarned += $day->earned;
                }
            }

            $historyData[$lastMonth] = [
                'views' => $totalViews,
                'earned' => $totalEarned
            ];

            $history = History::where('video_id', $video->id)->first();
            // FIX ID UPDATE PROBLEM
            $history->updateHistory($historyData);
        }
    }
}
