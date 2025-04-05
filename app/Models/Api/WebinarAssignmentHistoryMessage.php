<?php

namespace App\Models\Api;

use App\Models\Api\Traits\UploaderTrait;
use App\Models\WebinarAssignmentHistoryMessage as Model;

class WebinarAssignmentHistoryMessage extends Model
{
    use UploaderTrait;

    public function setFilePathAttribute($value)
    {
        $path = $this->storage($value);
        $this->attributes['file_path'] = $path;
    }
}
