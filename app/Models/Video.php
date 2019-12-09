<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
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
        return $this->belongsTo('App\Models\Channel');
    }

    public function history()
    {
        return $this->hasOne('App\Models\History');
    }

    public function videoDailyTracker()
    {
        return $this->hasOne('App\Models\VideoDailyTracker');
    }
}
