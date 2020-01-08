<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $primaryKey = 'db_id';

    protected $fillable = [
        'id',
        'user_id',
        'channel_db_id',
        'channel_id',
        'name',
        'tracked_zero',
        'month_zero',
        'treshold_zero',
        'earning_factor',
        'factor_currency',
        'treshold',
        'note'
    ];

    public function saveVideo($video)
	{
		return $this->create($video);
	}

    public function updateVideo($video)
	{
		return $this->update($video);
	}

    public function channel()
    {
        return $this->belongsTo('App\Models\Channel', 'db_id', 'channel_db_id');
    }

    public function history()
    {
        return $this->hasOne('App\Models\History', 'video_db_id', 'db_id');
    }

    public function videoDailyTracker()
    {
        return $this->hasOne('App\Models\VideoDailyTracker', 'video_db_id', 'db_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id');
    }

    // public static function boot()
    // {
    //     parent::boot();
    //
    //     static::deleting(function($video)
    //     {
    //         $video->history()->delete();
    //         $video->videoDailyTracker()->delete();
    //     });
    // }
}
