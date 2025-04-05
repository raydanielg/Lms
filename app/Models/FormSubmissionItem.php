<?php

namespace App\Models;

use App\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class FormSubmissionItem extends Model
{
    protected $table = "form_submission_items";
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];


    public function field()
    {
        return $this->belongsTo(FormField::class, 'form_field_id', 'id');
    }

}
