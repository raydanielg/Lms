<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebinarAssignmentResource extends JsonResource
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
            'title' => $this->title,
            'can_view_error' => $this->canViewError(),
            'description' => $this->description,
            'webinar_title' => $this->webinar->title,
            'webinar_image' => $this->webinar->getImage() ? url($this->webinar->getImage()) : null,
            'attempts' => $this->attempts ?? null,
            'pass_grade' => $this->pass_grade ?? null,
            'status' => $this->status,
            'min_grade' => $this->min_grade,
            'avg_grade' => $this->avg_grade,
            'total_grade' => $this->grade,
            'pending_count' => $this->pending_count,
            'passed_count' => $this->passed_count,
            'failed_count' => $this->failed_count,
            'submissions_count' => $this->submissions_count,
            'attachments' => $this->attachments->map(function ($item) {
                return [
                    'url' => $item->attach ? url($item->attach) : null,
                    'title' => $item->title,
                    'size' => $item->getFileSize(),

                ];
            }),
            'access_after_day' => $this->access_after_day,
            'check_previous_parts' => $this->check_previous_parts,
            'assignmentStatus' => $this->assignmentStatus,
        ];
    }

}
