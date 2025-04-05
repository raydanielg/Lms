<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class NavbarButtonTranslation extends Model
{
    protected $table = 'navbar_button_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
