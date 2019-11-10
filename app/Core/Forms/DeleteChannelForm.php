<?php

namespace App\Core\Forms;

use App\Core\Forms\Form;
use App\Models\Channel;
use App\Models\ChannelDailyTracker;
use App\Models\Video;
use App\Models\VideoDailyTracker;
use App\Models\History;

class DeleteChannelForm extends Form
{
    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function validate()
    {
        if(is_null($this->data['channelSettingsChannelId'])) return false;

        return true;
    }

    public function submit()
    {
        $channelId = $this->data['channelSettingsChannelId'];

        $channel = Channel::find($channelId);
        $channelDailyData = ChannelDailyTracker::where('channel_id', $channelId)->first();
        $videos = Video::where('channel_id', $channelId)->get();

        foreach($videos as $video)
        {
            $videoDailyData = VideoDailyTracker::where('video_id', $video->id)->first();
            $videoHistory = History::where('video_id', $video->id)->first();

            $video->delete();
            $videoDailyData->delete();
            $videoHistory->delete();
        }

        $channel->delete();
        $channelDailyData->delete();

        $this->setMessage('Channel "' . $channel->name . '" successfully deleted!');

        return true;
    }
}
