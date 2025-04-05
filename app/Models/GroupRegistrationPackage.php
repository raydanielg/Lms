<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupRegistrationPackage extends Model
{
    protected $table = 'groups_registration_packages';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
