<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BundleWebinar extends Model
{
    protected $table = 'bundle_webinars';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public function webinar()
    {
        return $this->belongsTo('App\Models\Webinar', 'webinar_id', 'id');
    }

    public function bundle()
    {
        return $this->belongsTo('App\Models\Bundle', 'bundle_id', 'id');
    }
}
