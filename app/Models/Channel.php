<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'tracking',
        'mode'
    ];

    public function saveChannel($channel)
	{
		return $this->create($channel);
	}

    public function updateChannel($channel)
	{
		return $this->update($channel);
	}

    public function videos()
    {
        return $this->hasMany('App\Models\Video');
    }

    public function channelDailyTracker()
    {
        return $this->hasOne('App\Models\ChannelDailyTracker');
    }
}
