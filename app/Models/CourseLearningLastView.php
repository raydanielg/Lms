<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLearningLastView extends Model
{
    protected $table = 'course_learning_last_views';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];


}
