<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentDeleteRequest extends Model
{
    protected $table = "content_delete_requests";
    public $timestamps = false;
    protected $guarded = ['id'];


    /* ==========
     | Relations
     * ==========*/

    public function targetable()
    {
        return $this->morphTo();
    }


    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function webinar()
    {
        return $this->belongsTo(Webinar::class, 'targetable_id')->where('targetable_type', 'App\Models\Webinar');
    }

    public function bundle()
    {
        return $this->belongsTo(Bundle::class, 'targetable_id')->where('targetable_type', 'App\Models\Bundle');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'targetable_id')->where('targetable_type', 'App\Models\Product');
    }

    public function post()
    {
        return $this->belongsTo(Blog::class, 'targetable_id')->where('targetable_type', 'App\Models\Blog');
    }


    /* ==========
     | Helpers
     * ==========*/
    public function getContentItem()
    {
        $item = null;

        switch ($this->targetable_type) {
            case "App\Models\Webinar":
                $item = Webinar::where('id', $this->targetable_id)->first();
                break;
            case "App\Models\Bundle":
                $item = Bundle::where('id', $this->targetable_id)->first();
                break;
            case "App\Models\Product":
                $item = Product::where('id', $this->targetable_id)->first();
                break;
            case "App\Models\Blog":
                $item = Blog::where('id', $this->targetable_id)->first();
                break;
        }

        return $item;
    }

    public function getContentType()
    {
        $type = "";

        switch ($this->targetable_type) {
            case "App\Models\Webinar":
                $type = "course";
                break;

            case "App\Models\Bundle":
                $type = "bundle";
                break;

            case "App\Models\Product":
                $type = "product";
                break;

            case "App\Models\Blog":
                $type = "post";
                break;
        }

        return $type;
    }

}
