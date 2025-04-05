<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Services\SlugService;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class ForumTopic extends Model
{
    use Sluggable;

    protected $table = 'forum_topics';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

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

    public function creator()
    {
        return $this->belongsTo('App\User', 'creator_id', 'id');
    }

    public function forum()
    {
        return $this->belongsTo('App\Models\Forum', 'forum_id', 'id');
    }

    public function attachments()
    {
        return $this->hasMany('App\Models\ForumTopicAttachment', 'topic_id', 'id');
    }

    public function likes()
    {
        return $this->hasMany('App\Models\ForumTopicLike', 'topic_id', 'id');
    }

    public function posts()
    {
        return $this->hasMany('App\Models\ForumTopicPost', 'topic_id', 'id');
    }

    public function getPostsUrl()
    {
        return "/forums/{$this->forum->slug}/topics/{$this->slug}/posts";
    }

    public function getLikeUrl()
    {
        return "/forums/{$this->forum->slug}/topics/{$this->slug}/likeToggle";
    }

    public function getBookmarkUrl()
    {
        return "/forums/{$this->forum->slug}/topics/{$this->slug}/bookmark";
    }

    public function getEditUrl()
    {
        return "/forums/{$this->forum->slug}/topics/{$this->slug}/edit";
    }
}
