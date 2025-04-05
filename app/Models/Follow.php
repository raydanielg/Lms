<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    public static $requested = "requested";
    public static $accepted = "accepted";
    public static $rejected = "rejected";
    
    public $timestamps = false;

    protected $guarded = ['id'];

}
