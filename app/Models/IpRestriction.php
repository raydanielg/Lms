<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpRestriction extends Model
{
    protected $table = "ip_restrictions";
    public $timestamps = false;
    protected $guarded = ['id'];


    /* ==========
     | Relations
     * ==========*/


    /* ==========
     | Helpers
     * ==========*/


}
