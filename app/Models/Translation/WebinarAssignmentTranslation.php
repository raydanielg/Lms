<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class WebinarAssignmentTranslation extends Model
{
    protected $table = 'webinar_assignment_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
