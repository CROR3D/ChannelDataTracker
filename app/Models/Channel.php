<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $primaryKey = 'db_id';

    protected $fillable = [
        'id',
        'user_id',
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
        return $this->hasMany('App\Models\Video', 'channel_db_id', 'db_id');
    }

    public function channelDailyTracker()
    {
        return $this->hasOne('App\Models\ChannelDailyTracker', 'channel_db_id', 'db_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function($channel)
        {
            $channel->videos()->delete();
            $channel->channelDailyTracker()->delete();
        });
    }
}
