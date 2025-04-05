<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TextLessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'index' => $this->index,
            'title' => $this->title,
            'can_view_error' => $this->canViewError(),
            'auth_has_read' => $this->read,
            //    'auth_has_access' => $this->auth_has_access,
            //    'user_has_access' => $this->user_has_access,

            'study_time' => $this->study_time,
            //    'order' => $this->order,
            'created_at' => $this->created_at,
            //    'accessibility' => $this->accessibility,
            //    'status' => $this->status,
            //    'updated_at' => $this->updated_at,
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
}
