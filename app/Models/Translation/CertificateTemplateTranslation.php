<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplateTranslation extends Model
{
    protected $table = 'certificate_template_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
