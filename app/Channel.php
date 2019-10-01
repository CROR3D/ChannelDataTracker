<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'tracking',
        'subs',
        'videos',
        'views'
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
        return $this->hasMany('App\Video');
    }

    public function dailyTracker()
    {
        return $this->hasOne('App\DailyTracker');
    }
}
