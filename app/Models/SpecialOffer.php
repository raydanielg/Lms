<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialOffer extends Model
{
    protected $table = 'special_offers';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public static $active = 'active';
    public static $inactive = 'inactive';

    public function webinar()
    {
        return $this->belongsTo('App\Models\Webinar', 'webinar_id', 'id');
    }

    public function bundle()
    {
        return $this->belongsTo('App\Models\Bundle', 'bundle_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function subscribe()
    {
        return $this->belongsTo('App\Models\Subscribe', 'subscribe_id', 'id');
    }

    public function registrationPackage()
    {
        return $this->belongsTo('App\Models\RegistrationPackage', 'registration_package_id', 'id');
    }

    public function getRemainingTimes()
    {
        $current_time = time();
        $date = $this->to_date;
        $difference = $date - $current_time;

        return time2string($difference);
    }
}
