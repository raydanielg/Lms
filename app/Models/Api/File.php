<?php

namespace App\Models\Api;

use App\Models\Api\Traits\CheckWebinarItemAccessTrait;
use App\Models\File as WebFile;

class File extends WebFile
{
    use CheckWebinarItemAccessTrait;

    public function getDetailsAttribute()
    {
        return [
            //  'icon_by_type' => $this->getIconByType(),
            'id' => $this->id,
            'title' => $this->title,
            'auth_has_read' => $this->read,
            'status' => $this->status,
            'order' => $this->order,
            'downloadable' => $this->downloadable,
            'accessibility' => $this->accessibility,
            'description' => $this->description,
            'storage' => $this->storage,
            'download_link' => $this->webinar->getUrl() . '/file/' . $this->id . '/download',
            'auth_has_access' => $this->auth_has_access,
            'user_has_access' => $this->user_has_access,
            'file' => $this->file(),
            //  'file' => $this->storage == 'local' ? url("/course/" . $this->webinar->slug . "/file/" . $this->id . "/play") : $this->file,
            'volume' => $this->volume,
            'file_type' => $this->file_type,
            'is_video' => $this->isVideo(),
            'interactive_type' => $this->interactive_type,
            'interactive_file_name' => $this->interactive_file_name,
            'interactive_file_path' => ($this->interactive_file_path) ? url($this->interactive_file_path) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'access_after_day' => $this->access_after_day,
            'check_previous_parts' => $this->check_previous_parts,
        ];

    }

    public function file()
    {
        if (!$this->file) {
            return null;
        }
        if (strstr($this->file, 'iframe') or strstr($this->file, 'https')) {
            return $this->file;
        }
        return url($this->file);
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

    public function webinar()
    {
        return $this->belongsTo('App\Models\Api\Webinar', 'webinar_id', 'id');
    }

    public function getReadAttribute()
    {
        $user = apiAuth();
        if (!$user) {
            return null;
        }

        return ($this->learningStatus()->where('user_id', $user->id)->count()) ? true : false;


    }
}
