<?php

namespace App\Http\Resources;

use App\Models\File;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $path = $this->file();
        if ($this->storage == 'upload_archive') {
            $path = $this->webinar->getUrl() . '/file/' . $this->id . '/showHtml';
        }
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'can_view_error' => $this->canViewError(),
            'auth_has_read' => $this->read,
            'auth_has_access' => $this->auth_has_access,
            'user_has_access' => $this->user_has_access,
            'file_type' => $this->file_type,
            'volume' => (empty($this->volume) or $this->volume === "0 bytes" or in_array($this->storage, File::$ignoreVolumeFileSources)) ? null : $this->volume,
            'storage' => $this->storage,
            'created_at' => $this->created_at,
            'download_link' => $this->webinar->getUrl() . '/file/' . $this->id . '/download',
            'file' => $path,
            'check_previous_parts' => $this->check_previous_parts,
            'access_after_day' => $this->access_after_day,

            /*  'downloadable' => $this->downloadable,
              'accessibility' => $this->accessibility,

              'storage' => $this->storage,
               'auth_has_access' => $this->auth_has_access,
              'user_has_access' => $this->user_has_access,
               //  'file' => $this->storage == 'local' ? url("/course/" . $this->webinar->slug . "/file/" . $this->id . "/play") : $this->file,

              'access_after_day' => $this->access_after_day,
              'check_previous_parts' => $this->check_previous_parts,
           //   'interactive_type' => $this->interactive_type,
           //   'interactive_file_name' => $this->interactive_file_name,
              'interactive_file_path' => ($this->interactive_file_path) ? url($this->interactive_file_path) : null,
              'updated_at' => $this->updated_at,*/
        ];
    }
}





