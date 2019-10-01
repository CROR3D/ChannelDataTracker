<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DailyTracker extends Model
{
    protected $table = 'daily_tracker';

    protected $fillable = [
        'channel_id',
        '1', '2', '3', '4', '5', '6', '7', '8', '9', '10',
        '11', '12', '13', '14', '15', '16', '17', '18', '19', '20',
        '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31'
    ];

    public function saveDailyTracker($dailyTracker)
	{
		return $this->create($dailyTracker);
	}

    public function updateDailyTracker($dailyTracker)
	{
		return $this->update($dailyTracker);
	}

    public function channel()
    {
        return $this->belongsTo('App\Channel');
    }
}
