<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscribeRemind extends Model
{
    protected $table = 'subscribe_reminds';
    public $timestamps = false;
    protected $guarded = ['id'];
}
