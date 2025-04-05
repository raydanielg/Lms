<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\CalendarLinks\Link;

class ReserveMeeting extends Model
{
    protected $table = "reserve_meetings";
    public static $open = "open";
    public static $finished = "finished";
    public static $pending = "pending";
    public static $canceled = "canceled";

    public $timestamps = false;

    protected $guarded = ['id'];

    public function meetingTime()
    {
        return $this->belongsTo('App\Models\MeetingTime', 'meeting_time_id', 'id');
    }

    public function meeting()
    {
        return $this->belongsTo('App\Models\Meeting', 'meeting_id', 'id');
    }

    public function sale()
    {
        return $this->belongsTo('App\Models\Sale', 'sale_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function session()
    {
        return $this->hasOne('App\Models\Session', 'reserve_meeting_id', 'id');
    }

    public function getDiscountPrice($user)
    {
        $price = $this->paid_amount;
        $totalDiscount = 0;

        if (!empty($this->discount)) {
            $totalDiscount += ($price * $this->discount) / 100;
        }

        if (!empty($user) and !empty($user->getUserGroup()) and isset($user->getUserGroup()->discount) and $user->getUserGroup()->discount > 0) {
            $totalDiscount += ($price * $user->getUserGroup()->discount) / 100;
        }

        return $totalDiscount;
    }

    public function addToCalendarLink()
    {
        $day = $this->day;
        $times = $this->meetingTime->time;
        $times = explode('-', $times);
        $start_time = date("H:i", strtotime($times[0]));
        $end_time = date("H:i", strtotime($times[1]));

        $startDate = \DateTime::createFromFormat('Y-m-d H:i', $day . ' ' . $start_time);
        $endDate = \DateTime::createFromFormat('Y-m-d H:i', $day . ' ' . $end_time);

        $link = Link::create('Meeting', $startDate, $endDate); //->description('Cookies & cocktails!')

        return $link->google();
    }
}
