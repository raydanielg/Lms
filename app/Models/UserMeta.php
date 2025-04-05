<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
    protected $table = 'users_metas';
    protected $guarded = ['id'];
    public $timestamps = false;

    // education,
    // experience
    // gender => man | woman
    // birthday
    // address
}
