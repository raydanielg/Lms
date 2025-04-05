<?php

namespace App\Models\Translation;


use Illuminate\Database\Eloquent\Model;

class HomePageStatisticTranslation extends Model
{

    protected $table = 'home_page_statistic_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
