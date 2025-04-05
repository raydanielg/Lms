<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseForumAnswer extends Model
{
    protected $table = 'course_forum_answers';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
