<?php

namespace App\Models;

use App\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class UserFormField extends Model
{
    protected $table = "user_form_fields";
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];


    public function field()
    {
        return $this->belongsTo(FormField::class, 'form_field_id', 'id');
    }

}
