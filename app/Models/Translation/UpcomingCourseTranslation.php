<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class UpcomingCourseTranslation extends Model
{
    protected $table = 'upcoming_course_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
