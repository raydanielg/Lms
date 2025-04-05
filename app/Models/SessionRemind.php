<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionRemind extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $table = 'session_reminds';
    protected $dateFormat = 'U';
}
