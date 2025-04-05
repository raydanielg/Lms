<?php

namespace App\Models;

use App\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    protected $table = "form_submissions";
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(FormSubmissionItem::class, 'submission_id', 'id');
    }

}
