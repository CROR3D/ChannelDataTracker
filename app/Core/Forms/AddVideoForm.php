<?php

namespace App\Core\Forms;

use APIManager;
use Carbon\Carbon;
use App\Core\Forms\Form;
use App\Models\Video;
use App\Models\VideoDailyTracker;
use App\Models\History;

class AddVideoForm extends Form
{
    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function validate()
    {
        if(is_null($this->data['videoSettingsVideoId']) || is_null($this->data['videoSettingsChannelId'])) return false;

        return true;
    }

    public function submit()
    {
        $videoId = $this->data['videoSettingsVideoId'];
        $channelId = $this->data['videoSettingsChannelId'];

        $today = Carbon::now();
        $day = $today->day;

        $videoExists = Video::where('id', $videoId)->exists();

        if($videoExists)
        {
            $this->setMessage('Video is already tracked!');

            return false;
        }

        $video = APIManager::getVideoData($videoId);

        if($video === null || empty($video->items))
        {
            $this->setMessage('Video not found!');

            return false;
        }

        $videoChannelId = $video->items[0]->snippet->channelId;

        if($videoChannelId !== $channelId)
        {
            $this->setMessage('Video doesn\'t belong to the channel you want to add it to!');

            return false;
        }

        $addVideo = [
            'id' => $videoId,
            'channel_id' => $channelId,
            'name' => ($this->data['videoSettingsTitle'] === null) ? $video->items[0]->snippet->title : $this->data['videoSettingsTitle'],
            'tracked_zero' => $video->items[0]->statistics->viewCount,
            'month_zero' => $video->items[0]->statistics->viewCount,
            'treshold_zero' => $video->items[0]->statistics->viewCount,
            'earning_factor' => $this->data['videoSettingsEarningFactor'],
            'factor_currency' => $this->data['videoSettingsFactorCurrency'],
            'treshold' => $this->data['videoSettingsTreshold'],
            'note' => $this->data['videoSettingsNote']
        ];

        $dailyData = [
            'video_id' => $videoId,
            'day' . $day => [
                'currentViews' => $video->items[0]->statistics->viewCount,
                'views' => 0,
                'earned' => 0
            ]
        ];

        $historyData = [
            'video_id' => $videoId
        ];

        $newVideo = new Video;
        $newVideo->saveVideo($addVideo);

        $newVideoDailyTracker = new VideoDailyTracker;
        $newVideoDailyTracker->saveVideoDailyTracker($dailyData);

        $newVideoHistory = new History;
        $newVideoHistory->saveHistory($historyData);

        $this->setMessage('Video "' . $addVideo['name'] . '" successfully added!');

        return true;
    }
}
