<?php

namespace App\Core\Forms;

use Sentinel;
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

        if(Sentinel::check())
        {
            $userId = Sentinel::getUser()->id;
        }
        else
        {
            $userId = null;
        }

        $channel = Channel::where('db_id', $channelId)->first();
        $channel->delete();

        $this->setMessage('Channel "' . $channel->name . '" successfully deleted!');

        return true;
    }
}
