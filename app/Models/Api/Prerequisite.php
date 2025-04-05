<?php

namespace App\Models\Api;

use App\Models\Prerequisite as Model;


class Prerequisite extends Model
{
    public function prerequisiteWebinar()
    {
        return $this->belongsTo('App\Models\Api\Webinar', 'prerequisite_id', 'id')
        ->where('status','active')->where('private',false) ;
        ;
    }
}
