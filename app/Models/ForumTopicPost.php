<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumTopicPost extends Model
{
    protected $table = 'forum_topic_posts';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function topic()
    {
        return $this->belongsTo('App\Models\ForumTopic', 'topic_id', 'id');
    }

    public function likes()
    {
        return $this->hasMany('App\Models\ForumTopicLike', 'topic_post_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\ForumTopicPost', 'parent_id', 'id');
    }

    public function getLikeUrl($forumSlug, $topicSlug)
    {
        return "/forums/{$forumSlug}/topics/{$topicSlug}/posts/{$this->id}/likeToggle";
    }

    public function getEditUrl($forumSlug, $topicSlug)
    {
        return "/forums/{$forumSlug}/topics/{$topicSlug}/posts/{$this->id}/edit";
    }

    public function getAttachmentUrl($forumSlug, $topicSlug)
    {
        return "/forums/{$forumSlug}/topics/{$topicSlug}/posts/{$this->id}/downloadAttachment";
    }

    public function getAttachmentName()
    {
        $name = "";

        if (!empty($this->attach)) {
            $attach = explode('/',$this->attach);

            $name = $attach[array_key_last($attach)];
        }

        return $name;
    }
}
