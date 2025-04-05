<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class BundleTranslation extends Model
{
    protected $table = 'bundle_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
