<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRegistrationPackage extends Model
{
    protected $table = 'users_registration_packages';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
