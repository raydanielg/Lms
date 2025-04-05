<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class FileTranslation extends Model
{
    protected $table = 'file_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
