<?php

namespace App\Models\Translation;


use Illuminate\Database\Eloquent\Model;

class FloatingBarTranslation extends Model
{

    protected $table = 'floating_bar_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
