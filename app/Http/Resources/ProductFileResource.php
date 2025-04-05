<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductFileResource extends JsonResource
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
            'description' => $this->description,
            'online_viewer' => (bool)$this->online_viewer,
            'online_view_url' => $this->when($this->online_viewer, $this->getOnlineViewUrl()),
            'download_url' => url($this->getDownloadUrl()),
        ];
    }
}








