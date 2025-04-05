<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    static $pending = 'pending';
    static $active = 'active';


    public function replies()
    {
        return $this->hasMany('App\Models\Comment', 'reply_id', 'id');
    }

    public function webinar()
    {
        return $this->belongsTo('App\Models\Webinar', 'webinar_id', 'id');
    }

    public function bundle()
    {
        return $this->belongsTo('App\Models\Bundle', 'bundle_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function review()
    {
        return $this->belongsTo('App\Models\WebinarReview', 'review_id', 'id');
    }

    public function blog()
    {
        return $this->belongsTo('App\Models\Blog', 'blog_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function productReview()
    {
        return $this->belongsTo('App\Models\ProductReview', 'product_review_id', 'id');
    }
}
