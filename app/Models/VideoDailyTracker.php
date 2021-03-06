<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoDailyTracker extends Model
{
    protected $table = 'video_daily_tracker';
    protected $primaryKey = 'video_id';

    public $incrementing = false;

    protected $fillable = [
        'video_id',
        'day1', 'day2', 'day3', 'day4', 'day5', 'day6', 'day7', 'day8', 'day9', 'day10',
        'day11', 'day12', 'day13', 'day14', 'day15', 'day16', 'day17', 'day18', 'day19', 'day20',
        'day21', 'day22', 'day23', 'day24', 'day25', 'day26', 'day27', 'day28', 'day29', 'day30', 'day31'
    ];

    protected $casts = [
        'day1' => 'array', 'day2' => 'array', 'day3' => 'array', 'day4' => 'array', 'day5' => 'array', 'day6' => 'array', 'day7' => 'array', 'day8' => 'array', 'day9' => 'array', 'day10' => 'array',
        'day11' => 'array', 'day12' => 'array', 'day13' => 'array', 'day14' => 'array', 'day15' => 'array', 'day16' => 'array', 'day17' => 'array', 'day18' => 'array', 'day19' => 'array', 'day20' => 'array',
        'day21' => 'array', 'day22' => 'array', 'day23' => 'array', 'day24' => 'array', 'day25' => 'array', 'day26' => 'array', 'day27' => 'array', 'day28' => 'array', 'day29' => 'array', 'day30' => 'array', 'day31' => 'array'
    ];

    public function saveVideoDailyTracker($videoDailyTracker)
	{
		return $this->create($videoDailyTracker);
	}

    public function updateVideoDailyTracker($videoDailyTracker)
	{
		return $this->update($videoDailyTracker);
	}

    public function video()
    {
        return $this->belongsTo('App\Models\Video');
    }
}
