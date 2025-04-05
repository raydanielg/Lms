<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class RegistrationPackageTranslation extends Model
{
    protected $table = 'registration_packages_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
