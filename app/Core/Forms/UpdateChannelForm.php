<?php

namespace App\Core\Forms;

use APIManager;
use App\Core\Forms\Form;
use App\Models\Channel;
use App\Models\Video;

class UpdateChannelForm extends Form
{
    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function validate()
    {
        foreach($this->data as $field => $value)
        {
            if($field == 'channelSettingsEarningFactor') continue;

            if(is_null($value)) return false;
        }

        return true;
    }

    public function submit()
    {
        $channelId = $this->data['channelSettingsChannelId'];

        $channelData = [
            'name' => $this->data['channelSettingsTitle'],
            'tracking' => $this->data['channelSettingsTracking'],
            'mode' => $this->data['channelSettingsTrackingMode']
        ];

        if($this->data['channelSettingsResetAllTresholds'] === 'true')
        {
            $allVideos = APIManager::getVideosData();

            foreach ($allVideos as $video) {
                $videoId = $video->items[0]->id;
                $videoNewTresholdZero = $video->items[0]->statistics->viewCount;
                $resetTreshold = [ 'treshold_zero' => $videoNewTresholdZero ];

                $dbVideo = Video::find($videoId);
                $dbVideo->updateVideo($resetTreshold);
            }
        }

        if($this->data['channelSettingsEarningFactor'])
        {
            $earningSettings = [
                'earning_factor' => $this->data['channelSettingsEarningFactor'],
                'factor_currency' => $this->data['channelSettingsFactorCurrency']
            ];

            $allChannelVideos = Video::where('channel_id', $channelId)->get();

            foreach ($allChannelVideos as $dbVideo)
            {
                $dbVideo = Video::find($dbVideo->id);
                $dbVideo->updateVideo($earningSettings);
            }
        }

        $channel = Channel::find($channelId);
        $channel->updateChannel($channelData);

        $this->setMessage('Channel "' . $channel->name . '" successfully updated!');

        return true;
    }
}
