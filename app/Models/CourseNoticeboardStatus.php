<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseNoticeboardStatus extends Model
{
    protected $table = 'course_noticeboard_status';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];//
}
