<?php

namespace App\Models\Api;

use App\Models\Meeting as Model;
use App\Http\Controllers\Api\Config\ConfigController;

class Meeting extends Model
{

    public function getDetailsAttribute()
    {

        return [
            'time_zone' => $this->getTimezone(),
            'gmt' => toGmtOffset($this->getTimezone()),
            'id' => $this->id,
            'disabled' => $this->disabled,
            'discount' => $this->discount,
            'price' => nicePrice($this->amount),
            'price_with_discount' => ($this->discount) ? nicePrice($this->amount - (($this->amount * $this->discount) / 100)) : $this->amount,

            'in_person' => $this->in_person,
            'in_person_price' => nicePrice($this->in_person_amount),
            'in_person_price_with_discount' =>
                nicePrice($this->in_person_amount - (($this->in_person_amount * $this->discount) / 100)),

            'in_person_group_min_student' => $this->in_person_group_min_student,
            'in_person_group_max_student' => $this->in_person_group_max_student,
            'in_person_group_amount ' => $this->in_person_group_amount,
            'group_meeting' => $this->group_meeting,

            'online_group_min_student' => $this->online_group_min_student,
            'online_group_max_student' => $this->online_group_max_student,
            'online_group_amount' => $this->online_group_amount,

            'timing' => $this->meetingTimes->map(function ($time) {
                return [
                    'id' => $time->id,
                    'day_label' => $time->day_label,
                    'time' => $time->time,
                ];
            }),
            'timing_group_by_day' => $this->meetingTimes->groupBy('day_label')->map(function ($time) {
                return $time->map(function ($ee) {
                    return [
                        'id' => $ee->id,
                        'day_label' => $ee->day_label,
                        'time' => $ee->time,
                    ];
                });

            }),

        ];

    }

    public function teacher()
    {
        return $this->belongsTo('App\Models\Api\User', 'teacher_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo('App\Models\Api\User', 'creator_id', 'id');
    }

    public function meetingTimes()
    {
        return $this->hasMany('App\Models\Api\MeetingTime', 'meeting_id', 'id');
    }

}
