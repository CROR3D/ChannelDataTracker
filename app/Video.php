<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'channel_id',
        'name',
        'views',
        'tracked_zero',
        'earning_factor',
        'factor_currency',
        'monthly_views',
        'treshold_views',
        'treshold',
        'likes',
        'dislikes',
        'comments',
        'note',
        'privacy'
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
        return $this->belongsTo('App\Channel');
    }

    public function history()
    {
        return $this->hasOne('App\History');
    }
}
