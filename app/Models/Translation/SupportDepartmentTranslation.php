<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class SupportDepartmentTranslation extends Model
{
    protected $table = 'support_department_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
