<?php

namespace App\Models\Api;

use App\Http\Controllers\Api\UploadFileManager;
use App\Models\Api\Traits\UploaderTrait;
use App\Models\CourseForum as Model;

class CourseForum extends Model
{
    use UploaderTrait;

    public function setAttachAttribute($value)
    {
        $path = $this->storage($value);
        $this->attributes['attach'] = $path ?: $this->attributes['attach']??null;
    }

    public function scopeHandleFilters($query)
    {
        $search = request()->get('search');

        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%$search%");
                $query->orWhere('description', 'like', "%$search%");
                $query->orWhereHas('answers', function ($query) use ($search) {
                    $query->where('description', 'like', "%$search%");
                });
            });
        }

        return $query;
    }
}
