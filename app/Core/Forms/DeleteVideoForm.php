<?php

namespace App\Core\Forms;

use Sentinel;
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

        if(Sentinel::check())
        {
            $userId = Sentinel::getUser()->id;
        }
        else
        {
            $userId = null;
        }

        $video = Video::where('id', $videoId)->where('user_id', $userId)->first();

        $video->delete();

        $this->setMessage('Video "' . $video->name . '" successfully deleted!');

        return true;
    }
}
