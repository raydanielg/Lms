<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model implements TranslatableContract
{
    use Translatable;
    use Sluggable;

    protected $table = 'blog_categories';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
    public $translatedAttributes = ['title'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public static function makeSlug($title)
    {
        return SlugService::createSlug(self::class, 'slug', $title);
    }

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }



    public function blog()
    {
        return $this->hasMany('App\Models\Blog', 'category_id', 'id');
    }

    public function getUrl()
    {
        return '/blog/categories/' . $this->slug;
    }
}
