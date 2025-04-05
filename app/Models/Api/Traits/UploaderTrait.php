<?php

namespace App\Models\Api\Traits;

trait UploaderTrait
{

    public function storage( $file)
    {
        if (!$file ) {
           return null;
        }
        $fileName = $file->getClientOriginalName();
        $path = apiAuth()->id;
        $storage_path = $file->storeAs($path, $fileName);
        return 'store/' . $storage_path;
    }
}
