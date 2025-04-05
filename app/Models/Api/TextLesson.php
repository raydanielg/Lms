<?php

namespace App\Models\Api;

use App\Models\Api\Traits\CheckWebinarItemAccessTrait;
use App\Models\TextLesson as WebTextLesson;

class TextLesson extends WebTextLesson
{

    use CheckWebinarItemAccessTrait ;

    public function getDetailsAttribute()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'auth_has_read' => $this->read,
            'auth_has_access' => $this->auth_has_access,
            'user_has_access' => $this->user_has_access,

            'study_time' => $this->study_time,
            'order' => $this->order,
            'created_at' => $this->created_at,
            'accessibility' => $this->accessibility,
            'status' => $this->status,
            'updated_at' => $this->updated_at,
            'summary' => $this->summary,
            'content' => $this->content,
            'locale' => $this->locale,
            // 'read'=>$this->read ,
            'attachments' => $this->attachments()->get()->map(function ($attachment) {
                return $attachment->details;
            }),
            'attachments_count' => $this->attachments()->count(),
            'access_after_day' => $this->access_after_day,
            'check_previous_parts' => $this->check_previous_parts,

        ];
    }

    public function getUserHasAccessAttribute()
    {

        $user = apiAuth();
        $access = false;
        $hasBought = $this->webinar->checkUserHasBought($user);
        if ($this->accessibility == 'paid') {
            if ($user and $hasBought) {
                $access = true;
            }
        } else {
            $access = true;
        }
        return $access;
    }

    public function getAuthHasAccessAttribute()
    {
        $user = apiAuth();
        $canAccess = null;
        if ($user) {
            $canAccess = true;
            if ($this->accessibility == 'paid') {
                $canAccess = ($this->webinar->checkUserHasBought($user)) ? true : false;
            }
        }

        return $canAccess;
    }

    public function getReadAttribute()
    {
        $user = apiAuth();
        if (!$user) {
            return null;
        }

        return ($this->learningStatus()->where('user_id', $user->id)->count()) ? true : false;


    }

    public function attachments()
    {
        return $this->hasMany('App\Models\Api\TextLessonAttachment', 'text_lesson_id', 'id');
    }
    public function webinar()
    {
        return $this->belongsTo('App\Models\Api\Webinar', 'webinar_id', 'id');
    }
}
