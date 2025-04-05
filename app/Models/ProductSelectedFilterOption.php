<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSelectedFilterOption extends Model
{
    protected $table = 'product_selected_filter_options';
    public $timestamps = false;
    protected $guarded = ['id'];
}
