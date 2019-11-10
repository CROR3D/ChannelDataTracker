<?php

namespace App\Core\Forms;

use App\Core\Forms\Form;
use App\Models\Video;

class UpdateVideoForm extends Form
{
    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function validate()
    {
        foreach($this->data as $field => $value)
        {
            if($field == 'videoSettingsNote') continue;

            if(is_null($value)) return false;
        }

        return true;
    }

    public function submit()
    {
        $videoId = $this->data['videoSettingsVideoId'];

        $videoData = [
            'name' => $this->data['videoSettingsTitle'],
            'earning_factor' => $this->data['videoSettingsEarningFactor'],
            'factor_currency' => $this->data['videoSettingsFactorCurrency'],
            'treshold' => $this->data['videoSettingsTreshold'],
            'note' => $this->data['videoSettingsNote']
        ];

        $video = Video::find($videoId);
        $video->updateVideo($videoData);

        $this->setMessage('Video "' . $video->name . '" successfully updated!');

        return true;
    }
}
