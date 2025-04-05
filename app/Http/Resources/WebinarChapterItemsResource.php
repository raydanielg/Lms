<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebinarChapterItemsResource extends JsonResource
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
            'can' => [
                'view' => (!empty($this->resource->item) && !$this->resource->item->canViewError() and (($this->type == 'file' and $this->resource->item->user_has_access) or ($this->type != 'file'))),
            ],
            'can_view_error' => !empty($this->resource->item) && $this->resource->item->canViewError(),
            'auth_has_read' => !empty($this->resource->item) && $this->resource->item->read,
            //    'ff' => $this->resource->item->checkSequenceContent(apiAuth()),
            //  'id' => $this->id,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'link' => !empty($this->resource->item) ? route($this->type . '.show', $this->resource->item->id) : null,
            $this->merge($this->resource->getItemResource()),
        ];
    }
}
