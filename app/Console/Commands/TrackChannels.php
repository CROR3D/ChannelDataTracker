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
            $dbChannel = Channel::find($channelId);
            $channelDailyTracker = ChannelDailyTracker::where('channel_id', $channelId)->first();
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

            $yesterdayData = $channelDailyTracker->{'day' . $getYesterday};

            $currentViews = $data->statistics->viewCount;
            $currentVideos = $data->statistics->videoCount;
            $currentSubs = $data->statistics->subscriberCount;

            $dailyViews = $currentViews - $yesterdayData['views'];
            $dailyVideos = $currentVideos - $yesterdayData['videos'];
            $dailySubs = $currentSubs - $yesterdayData['subs'];

            $updateData = [
                $today => [
                    'currentViews' => $currentViews,
                    'currentSubs' => $currentSubs,
                    'currentVideos' => $currentVideos,
                    'views' => $dailyViews,
                    'subs' => $dailySubs,
                    'videos' => $dailyVideos
                ]
            ];

            // TODO: IF UPDATE DATA IS THE SAME AS BEFORE (if returns false)
            $channelDailyTracker->updateChannelDailyTracker($updateData);
        }
    }
}
