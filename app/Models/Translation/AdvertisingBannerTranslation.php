<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class AdvertisingBannerTranslation extends Model
{
    protected $table = 'advertising_banners_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
