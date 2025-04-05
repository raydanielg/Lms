<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Services\SlugService;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Forum extends Model implements TranslatableContract
{
    use Translatable;
    use Sluggable;

    protected $table = 'forums';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public $translatedAttributes = ['title', 'description'];

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    public function getDescriptionAttribute()
    {
        return getTranslateAttributeValue($this, 'description');
    }

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

    public function parent()
    {
        return $this->belongsTo('App\Models\Forum', 'parent_id', 'id');
    }

    public function subForums()
    {
        return $this->hasMany($this, 'parent_id', 'id')->orderBy('order', 'asc');
    }

    public function topics()
    {
        return $this->hasMany('App\Models\ForumTopic', 'forum_id', 'id');
    }

    public function getUrl()
    {
        return '/forums/' . $this->slug . '/topics';
    }

    public function isClosed()
    {
        $close = $this->close;

        if (!$close and !empty($this->parent_id)) {
            $parent = $this->parent;

            if (!empty($parent)) {
                $close = $parent->close;
            }
        }

        return $close;
    }

    public function checkUserCanCreateTopic($user = null)
    {
        $result = true;

        if (empty($user)) {
            $user = auth()->user();
        }

        if (!empty($this->group_id)) {
            $result = false;

            if (!empty($user)) {
                $userGroup = $user->userGroup;

                if (!empty($userGroup) and $userGroup->group_id == $this->group_id) {
                    $result = true;
                }
            }
        }

        if (!empty($this->role_id) and $result) {
            $result = (!empty($user) and $this->role_id == $user->role_id);
        }

        return $result;
    }
}
