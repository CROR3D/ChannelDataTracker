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
        return true;
    }

    public function submit()
    {
        $videoId = $this->data['videoSettingsVideoId'];

        $video = Video::find($videoId);

        $video->delete();

        return true;
    }
}
