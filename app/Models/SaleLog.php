<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleLog extends Model
{
    protected $table = 'sales_log';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
