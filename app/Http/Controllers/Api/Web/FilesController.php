<?php

namespace App\Http\Controllers\Api\Web ;
use App\Http\Controllers\Api\Controller ;
use App\Models\Api\Webinar ;
use App\Models\Api\File ;

class FilesController extends Controller{


    public function download($file_id)
    {
        $file=File::find($file_id) ;

        if(!$file){
            abort(404) ;
        }
        $webinar=$file->webinar()->where('private', false)
        ->where('status', 'active')->first() ;

        if(!$webinar){
            abort(404) ;
         }

        if(!$file->downloadable){
            return apiResponse2(1, 'not_downloadable', trans('api.file.not_downloadable'));
        }

        $canAccess = true;

        if ($file->accessibility == 'paid') {
            $canAccess = $webinar->checkUserHasBought(apiAuth());
        }

        if(!$canAccess){
            return apiResponse2(1, 'not_accessible', trans('api.file.not_accessible'));

        }

        $filePath = public_path($file->file);

        $fileName = str_replace(' ', '-', $file->title);
        $fileName = str_replace('.', '-', $fileName);
        $fileName .= '.' . $file->file_type;

        $headers = array(
            'Content-Type: application/' . $file->file_type,
        );

        return response()->download($filePath, $fileName, $headers);
       
    }

}