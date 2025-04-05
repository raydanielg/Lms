<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCookieSecurity extends Model
{
    protected $table = 'users_cookie_security';
    public $timestamps = false;
    protected $guarded = ['id'];

    static $types = ['all', 'customize'];
    static $ALL = 'all';
    static $CUSTOMIZE = 'customize';
}
