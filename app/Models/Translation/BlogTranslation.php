<?php

namespace App\Models\Translation;


use Illuminate\Database\Eloquent\Model;

class BlogTranslation extends Model
{

    protected $table = 'blog_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
