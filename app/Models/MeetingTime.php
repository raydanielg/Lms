<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingTime extends Model
{

    public static $open = "open";
    public static $finished = "finished";

    public static $saturday = "saturday";
    public static $sunday = "sunday";
    public static $monday = "monday";
    public static $tuesday = "tuesday";
    public static $wednesday = "wednesday";
    public static $thursday = "thursday";
    public static $friday = "friday";
    public static $days = ["saturday", "sunday", "monday", "tuesday", "wednesday", "thursday", "friday"];

    public $timestamps = false;

    protected $guarded = ['id'];

    public function meeting()
    {
        return $this->belongsTo('App\Models\Meeting', 'meeting_id', 'id');
    }
}
