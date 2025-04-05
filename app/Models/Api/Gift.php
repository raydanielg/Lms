<?php

namespace App\Models\Api;

use App\Models\Gift as Model;

class Gift extends Model
{



    public function sale()
    {
        return $this->hasOne('App\Models\Api\Sale', 'gift_id', 'id');
    }

    public function webinar()
    {
        return $this->belongsTo('App\Models\Api\Webinar', 'webinar_id', 'id');
    }

    public function bundle()
    {
        return $this->belongsTo('App\Models\Api\Bundle', 'bundle_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Api\Product', 'product_id', 'id');
    }


}
