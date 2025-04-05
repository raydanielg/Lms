<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prerequisite extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    public function prerequisiteWebinar()
    {
        return $this->belongsTo('App\Models\Webinar', 'prerequisite_id', 'id');
    }
}
