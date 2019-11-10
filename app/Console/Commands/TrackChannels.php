<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use APIManager;
use App\Models\Channel;
use App\Models\ChannelDailyTracker;
use Carbon\Carbon;

class TrackChannels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:trackchannels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store channel daily data in database';

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
        $channels = APIManager::getChannelsData();

        foreach ($channels as $channel) {
            $data = $channel->items[0];
            $channelId = $data->id;
            $channelDailyTracker = ChannelDailyTracker::where('channel_id', $channelId)->first();
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

            $channelDailyTracker->updateChannelDailyTracker($updateData);
        }
    }
}
