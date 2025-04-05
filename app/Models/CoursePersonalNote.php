<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursePersonalNote extends Model
{
    protected $table = "course_personal_notes";
    public $timestamps = false;
    protected $guarded = ['id'];


    /* ==========
     | Relations
     * ==========*/

    public function targetable()
    {
        return $this->morphTo();
    }

    public function course()
    {
        return $this->belongsTo(Webinar::class, 'course_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    /* ==========
     | Helpers
     * ==========*/
    public function getItemType()
    {
        $type = "";

        switch ($this->targetable_type) {
            case "App\Models\Session":
                $type = "session";
                break;

            case "App\Models\File":
                $type = "file";
                break;

            case "App\Models\Quiz":
                $type = "quiz";
                break;

            case "App\Models\TextLesson":
                $type = "text_lesson";
                break;

            case "App\Models\WebinarAssignment":
                $type = "assignment";
                break;
        }

        return $type;
    }

    public function getItem()
    {
        $query = null;
        $type = $this->getItemType();

        switch ($type) {
            case 'session' :
                $query = Session::query();
                break;
            case 'file' :
                $query = File::query();
                break;
            case 'quiz' :
                $query = Quiz::query();
                break;
            case 'text_lesson' :
                $query = TextLesson::query();
                break;
            case 'assignment' :
                $query = WebinarAssignment::query();
                break;
        }

        return $query->where('id', $this->targetable_id)->first();
    }

}
