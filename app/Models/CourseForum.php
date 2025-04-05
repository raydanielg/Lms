<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseForum extends Model
{
    protected $table = 'course_forums';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function webinar()
    {
        return $this->belongsTo('App\Models\Webinar', 'webinar_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany('App\Models\CourseForumAnswer', 'forum_id', 'id');
    }
}
