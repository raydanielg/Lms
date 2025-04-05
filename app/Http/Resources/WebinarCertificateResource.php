<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebinarCertificateResource extends JsonResource
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
            'webinar' => [
                'id' => $this->webinar->id,
                'title' => $this->webinar->title,
                'image' => $this->webinar->getImage() ? url($this->webinar->getImage()) : null,
                'description' => $this->webinar->description,
            ],
            //    'webinar_title' => $this->webinar->title,
            //   'webinar_description' => $this->webinar->description,
            'link' => route('webinar.certificate', $this->id),
            'date' => $this->created_at,

        ];
    }
}
