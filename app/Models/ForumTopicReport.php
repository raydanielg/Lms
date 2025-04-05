<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumTopicReport extends Model
{
    protected $table = 'forum_topic_reports';
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

    public function topicPost()
    {
        return $this->belongsTo('App\Models\ForumTopicPost', 'topic_post_id', 'id');
    }
}
