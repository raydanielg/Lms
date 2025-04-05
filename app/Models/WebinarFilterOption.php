<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebinarFilterOption extends Model
{
    protected $table = 'webinar_filter_option';
    public $timestamps = false;

    protected $guarded = ['id'];
}
