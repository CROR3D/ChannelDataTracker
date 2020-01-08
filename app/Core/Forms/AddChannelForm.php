<?php

namespace App\Core\Forms;

use Sentinel;
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
        if(is_null($this->data['addChannelID'])) return false;

        return true;
    }

    public function submit()
    {
        $channelId = $this->data['addChannelID'];
        $channelAPIData = APIManager::getChannelData($channelId);

        if(Sentinel::check())
        {
            $userId = Sentinel::getUser()->id;
        }
        else
        {
            $userId = null;
        }

        if(!$channelAPIData)
        {
            $this->setMessage('Can not add channel by ID! Data connection lost!');

            return false;
        }

        $channelExists = Channel::where('id', $channelId)->where('user_id', $userId)->exists();

        if($channelExists)
        {
            $this->setMessage('Channel is already tracked!');

            return false;
        }

        $channelData = $channelAPIData->items[0];
        $getToday = Carbon::now()->day;

        $currentViews = $channelData->statistics->viewCount;
        $currentVideos = $channelData->statistics->videoCount;
        $currentSubs = $channelData->statistics->subscriberCount;

        $channel = [
            'id' => $channelId,
            'user_id' => $userId,
            'name' => $channelData->snippet->title
        ];

        $newChannel = new Channel;
        $newChannel->saveChannel($channel);

        $dbChannel = Channel::where('id', $channelId)->where('user_id', $userId)->first();
        $dbChannelId = $dbChannel->db_id;

        $dailyData = [
            'channel_db_id' => $dbChannelId,
            'user_id' => $userId,
            'channel_id' => $channelId,
            'day' . $getToday => [
                'currentViews' => $currentViews,
                'currentSubs' => $currentSubs,
                'currentVideos' => $currentVideos,
                'subs' => 0,
                'videos' => 0,
                'views' => 0
            ]
        ];

        $newChannelDailyTracker = new ChannelDailyTracker;
        $newChannelDailyTracker->saveChannelDailyTracker($dailyData);

        $this->setMessage('Channel "' . $channel['name'] . '" successfully added!');

        return true;
    }
}
