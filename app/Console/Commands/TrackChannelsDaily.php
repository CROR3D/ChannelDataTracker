<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Facades\ScheduleHelper;
use App\Channel;
use App\DailyTracker;
use Carbon\Carbon;

class TrackChannelsDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:trackchannelsdaily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store channels data in database every day at 00:00';

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
        $channels = ScheduleHelper::getCurrentChannelsData();

        foreach ($channels as $channel) {
            $data = $channel->items[0];
            $channelId = $data->id;
            $dailyTracker = DailyTracker::where('channel_id', $channelId)->first();
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
                    'subs' => $data->statistics->subscriberCount,
                    'videos' => $data->statistics->videoCount,
                    'views' => $data->statistics->viewCount
                ]
            ];

            $dailyTracker->updateDailyTracker($updateData);
        }
    }
}
