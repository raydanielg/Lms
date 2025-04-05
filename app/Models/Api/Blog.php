<?php

namespace App\Models\Api;

use App\Models\Blog as Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Blog extends Model implements TranslatableContract
{
    use Translatable;

    public $translatedAttributes = ['title', 'description', 'meta_description', 'content'];


    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    public function getDescriptionAttribute()
    {
        return getTranslateAttributeValue($this, 'description');
    }

    public function getMetaDescriptionAttribute()
    {
        return getTranslateAttributeValue($this, 'meta_description');
    }

    public function getContentAttribute()
    {
        return getTranslateAttributeValue($this, 'content');
    }
    public  function getDetailsAttribute()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' =>($this->image)? url($this->image):null,
            'description' => truncate($this->description, 160),
            'content' => $this->content,
            'created_at' => $this->created_at,
            'locale'=>$this->locale ,
            'author' => $this->author->brief,
            'comment_count' => $this->comments()->where('status','active')->count(),
            'comments' => $this->comments()->where('status','active')
                ->get()->map(function ($item) {
                    return $item->details ;
                }),
            'category'=>$this->category->title ,
            'badges'=>$this->badges ?? [] ,
        ];
    }
    public function getBriefAttribute(){
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' =>($this->image)? url($this->image):null,
            'description' => truncate($this->description, 160),
            'created_at' => $this->created_at,
            'author' => $this->author->brief,
            'comment_count' => $this->comments->count(),
            'category'=>$this->category->title ,
        ];
    }
    public function badges()
    {
        return $this->hasMany('App\Models\Api\ProductBadgeContent', 'targetable_id', 'id');
    }
    public function author()
    {
        return $this->belongsTo('App\Models\Api\User', 'author_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Api\Comment', 'blog_id', 'id');
    }

    public function scopeHandleFilters( $query)
    {
        $request=request() ;
        $offset = $request->get('offset', null);
        $limit = $request->get('limit', null);
        $category=$request->get('cat',null) ;

        if (!empty($offset) && !empty($limit)) {
            $query->skip($offset);
        }
        if (!empty($limit)) {
            $query->take($limit);
        }

        if($category){
            $query->where('category_id',$category) ;
        }

        return $query;
    }

}
