<?php

namespace App\Models\Translation;


use Illuminate\Database\Eloquent\Model;

class BlogCategoryTranslation extends Model
{

    protected $table = 'blog_category_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
