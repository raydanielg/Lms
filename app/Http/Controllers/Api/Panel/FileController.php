<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\FileResource;
use App\Models\Api\File;
use App\Models\WebinarChapter;

class FileController extends Controller
{
    public function show($file_id)
    {
        $file = File::where('id', $file_id)
            ->where('files.status', WebinarChapter::$chapterActive)->first();
        abort_unless($file, 404);
        if ($error = $file->canViewError()) {
                   return $this->failure($error, 403, 403);
        }
        $resource = new FileResource($file);
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $resource);
    }
}
