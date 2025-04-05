<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];
}
