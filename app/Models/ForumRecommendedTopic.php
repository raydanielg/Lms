<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumRecommendedTopic extends Model
{
    protected $table = 'forum_recommended_topics';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];


    public function topics()
    {
        return $this->belongsToMany('App\Models\ForumTopic', 'forum_recommended_topic_items',
            'recommended_topic_id', 'topic_id');
    }
}
