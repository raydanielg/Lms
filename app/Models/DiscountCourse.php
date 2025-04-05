<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCourse extends Model
{
    protected $table = 'discount_courses';
    public $timestamps = false;

    protected $guarded = ['id'];

    public function discount()
    {
        return $this->belongsTo('App\Models\Discount', 'discount_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo('App\Models\Webinar', 'course_id', 'id');
    }
}
