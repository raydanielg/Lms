<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumTopicLike extends Model
{
    protected $table = 'forum_topic_likes';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
