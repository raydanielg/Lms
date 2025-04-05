<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class ProductFileTranslation extends Model
{
    protected $table = 'product_file_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
