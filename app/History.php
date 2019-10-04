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

    protected $casts = [
        'january' => 'array',
        'february' => 'array',
        'march' => 'array',
        'april' => 'array', 
        'may' => 'array',
        'june' => 'array',
        'july' => 'array',
        'august' => 'array',
        'september' => 'array',
        'october' => 'array',
        'november' => 'array',
        'december' => 'array'
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
