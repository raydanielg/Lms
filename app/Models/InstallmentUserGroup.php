<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class InstallmentUserGroup extends Model
{
    protected $table = 'installment_user_groups';
    public $timestamps = false;
    protected $guarded = ['id'];


}
