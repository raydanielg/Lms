<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebinarReview extends Model
{
    protected $table = 'webinar_reviews';
    public $timestamps = false;

    protected $guarded = ['id'];

    public function webinar()
    {
        return $this->belongsTo('App\Models\Webinar', 'webinar_id', 'id');
    }

    public function bundle()
    {
        return $this->belongsTo('App\Models\Bundle', 'bundle_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'creator_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment', 'review_id', 'id');
    }
}
