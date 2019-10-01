<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'history';

    protected $fillable = [
        'video_id',
        'tresholds_reached',
        'current_month',
        'january',
        'february',
        'march',
        'april',
        'may',
        'june',
        'july',
        'august',
        'september',
        'october',
        'november',
        'december',
    ];

    public function saveHistory($history)
	{
		return $this->create($history);
	}

    public function updateHistory($history)
	{
		return $this->update($history);
	}

    public function video()
    {
        return $this->belongsTo('App\Video');
    }
}
