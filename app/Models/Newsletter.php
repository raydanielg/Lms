<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    protected $table = 'newsletters';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
