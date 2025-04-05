<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class FeatureWebinarTranslation extends Model
{
    protected $table = 'feature_webinar_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
