<?php

namespace App\Core\Forms;

use App\Core\Forms\Form;
use App\Models\Video;

class DeleteVideoForm extends Form
{
    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function validate()
    {
        if(is_null($this->data['videoSettingsVideoId'])) return false;

        return true;
    }

    public function submit()
    {
        $videoId = $this->data['videoSettingsVideoId'];

        $video = Video::find($videoId);

        $video->delete();

        $this->setMessage('Video "' . $video->name . '" successfully deleted!');

        return true;
    }
}
