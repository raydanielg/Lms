<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebinarAssignmentHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public $student_details = false;

    public function toArray($request)
    {

        return [
            'id' => $this->assignment->id,
            'title' => $this->assignment->title,
            'deadline' => $this->deadlineDays(),
            $this->mergeWhen($this->student_details, [
                'student' => $this->student->id
            ]),
            'student' => [
                'id' => $this->student->id,
                'full_name' => $this->student->full_name,
                'email'=>$this->student->email ,
                'avatar' => $this->student->getAvatar() ? url($this->student->getAvatar()) : null,

            ],
//deadline_time
            $this->mergeWhen($this->student_id == apiAuth()->id, [
                'deadline_time' => ($this->student_id == apiAuth()->id) ? $this->assignment->deadlineTime : null
            ]),

            'can' => [
                'send_message' => $this->canSendMessage()
            ],
            'can_view_error' => $this->assignment->canViewError(),
            'description' => $this->assignment->description,
            'webinar_title' => $this->assignment->webinar->title,
            'webinar_image' => url($this->assignment->webinar->getImage()),
            'first_submission' => $this->first_submission ?? null,
            'last_submission' => $this->last_submission ?? null,
            'attempts' => $this->assignment->attempts ?? null,
            'used_attempts_count' => $this->used_attempts_count ?? 0,
            'grade' => $this->grade ?? null,
            'total_grade' => $this->assignment->grade,
            'pass_grade' => $this->assignment->pass_grade ?? null,
            'purchase_date' => $this->sale() ? $this->sale()->created_at : null,
            'user_status' => $this->status,
            'attachments' => $this->assignment->attachments->map(function ($item) {
                return [
                    'url' => $item->attach ? url($item->attach) : null,
                    'title' => $item->title,
                    'size' => $item->getFileSize(),

                ];
            })
        ];
    }
}
