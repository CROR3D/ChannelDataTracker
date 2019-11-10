<?php

namespace App\Core\Forms;

use APIManager;
use Carbon\Carbon;
use App\Core\Forms\Form;
use App\Models\Channel;
use App\Models\ChannelDailyTracker;

class AddChannelForm extends Form
{
    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function validate()
    {
        return true;
    }

    public function submit()
    {
        $channelId = $this->data['addChannelID'];
        $channelAPIData = APIManager::getChannelData($channelId);
        $channelData = $channelAPIData->items[0];

        $channelId = $channelData->id;
        $today = Carbon::now();
        $day = $today->day;

        $channel = [
            'id' => $channelId,
            'name' => $channelData->snippet->title
        ];

        $dailyData = [
            'channel_id' => $channelId,
            'day' . $day => [
                'subs' => $channelData->statistics->subscriberCount,
                'videos' => $channelData->statistics->videoCount,
                'views' => $channelData->statistics->viewCount
            ]
        ];

        $newChannel = new Channel;
        $newChannel->saveChannel($channel);

        $newChannelDailyTracker = new ChannelDailyTracker;
        $newChannelDailyTracker->saveChannelDailyTracker($dailyData);

        return true;
    }
}
