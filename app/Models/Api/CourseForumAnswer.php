<?php

namespace App\Models\Api;

use App\Models\CourseForum;
use App\Models\CourseForumAnswer as Model;

class CourseForumAnswer extends Model
{
    //
    public function course_forum()
    {
       return $this->belongsTo(CourseForum::class, 'forum_id');
    }
}
