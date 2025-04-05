<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class UserSelectedBank extends Model
{
    protected $table = "user_selected_banks";
    public $timestamps = false;

    protected $guarded = ['id'];


    public function bank()
    {
        return $this->belongsTo('App\Models\UserBank', 'user_bank_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function specifications()
    {
        return $this->hasMany('App\Models\UserSelectedBankSpecification', 'user_selected_bank_id', 'id');
    }
}
