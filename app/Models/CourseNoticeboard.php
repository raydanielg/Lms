<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseNoticeboard extends Model
{
    protected $table = 'course_noticeboards';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    static $colors = ['warning', 'danger', 'neutral', 'info', 'success'];

    public function webinar()
    {
        return $this->belongsTo('App\Models\Webinar', 'webinar_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'creator_id', 'id');
    }

    public function noticeboardStatus()
    {
        return $this->hasOne('App\Models\CourseNoticeboardStatus', 'noticeboard_id', 'id');
    }

    public function getIcon()
    {
        $icons = [
            'warning' => 'alert-triangle',
            'danger' => 'alert-octagon',
            'neutral' => 'shield',
            'info' => 'message-square',
            'success' => 'check-circle'
        ];

        return $icons[$this->color];
    }
}
