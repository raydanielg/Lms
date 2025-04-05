<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class InstallmentReminder extends Model
{
    protected $table = 'installment_reminders';
    public $timestamps = false;
    protected $guarded = ['id'];

}
