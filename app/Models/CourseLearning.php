<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLearning extends Model
{
    protected $table = 'course_learning';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];


}
