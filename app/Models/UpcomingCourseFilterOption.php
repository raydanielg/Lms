<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpcomingCourseFilterOption extends Model
{
    protected $table = 'upcoming_course_filter_option';
    public $timestamps = false;

    protected $guarded = ['id'];
}
