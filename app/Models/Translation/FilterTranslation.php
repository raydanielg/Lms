<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class FilterTranslation extends Model
{
    protected $table = 'filter_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
