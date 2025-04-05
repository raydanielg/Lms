<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumRecommendedTopicItem extends Model
{
    protected $table = 'forum_recommended_topic_items';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
