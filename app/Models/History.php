<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'history';
    protected $primaryKey = 'db_id';

    public $incrementing = false;

    protected $fillable = [
        'video_id',
        'video_db_id',
        'user_id',
        'tresholds_reached',
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
        return $this->belongsTo('App\Models\Video', 'db_id', 'video_db_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id');
    }

    // public static function boot()
    // {
    //     parent::boot();
    //
    //     static::deleting(function($history)
    //     {
    //          $history->video()->delete();
    //     });
    // }
}
