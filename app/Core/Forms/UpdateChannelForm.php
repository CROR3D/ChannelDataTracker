<?php

namespace App\Core\Forms;

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
            'tracking' => $this->data['channelSettingsTracking']
        ];

        if($this->data['channelSettingsEarningFactor'])
        {
            $allVideoData = [
                'earning_factor' => $this->data['channelSettingsEarningFactor'],
                'factor_currency' => $this->data['channelSettingsFactorCurrency']
            ];

            $allChannelVideos = Video::where('channel_id', $channelId)->get();

            foreach ($allChannelVideos as $video)
            {
                $video = Video::find($video->id);
                $video->updateVideo($allVideoData);
            }
        }

        $channel = Channel::find($channelId);
        $channel->updateChannel($channelData);

        $this->setMessage('Channel "' . $channel->name . '" successfully updated!');

        return true;
    }
}
