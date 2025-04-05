<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
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
          //  'title' => $this->id,
            'webinar_title' => $this->webinar->title,
            'pass_mark' => $this->pass_mark,
            'average_grade' => $this->average_grade,
            'certificates_count' => $this->certificates->count(),
            'created_at' => $this->created_at,
        ];
    }
}


