<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class FilterOptionTranslation extends Model
{
    protected $table = 'filter_option_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
