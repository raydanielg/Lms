<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BundleFilterOption extends Model
{
    protected $table = 'bundle_filter_option';
    public $timestamps = false;

    protected $guarded = ['id'];
}
