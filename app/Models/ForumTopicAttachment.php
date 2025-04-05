<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumTopicAttachment extends Model
{
    protected $table = 'forum_topic_attachments';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public function getDownloadUrl($forumSlug, $topicSlug)
    {
        return "/forums/{$forumSlug}/topics/{$topicSlug}/downloadAttachment/{$this->id}";
    }

    public function getName()
    {
        $name = "";

        if (!empty($this->path)) {
            $path = explode('/',$this->path);

            $name = $path[array_key_last($path)];
        }


        return $name;
    }
}
