<?php
namespace App\Models\Api ;
use App\Models\TextLessonAttachment as PrimaryModel;

class TextLessonAttachment extends PrimaryModel {
    
    public function file()
    {
        return $this->belongsTo('App\Models\Api\File', 'file_id', 'id');
    }

    public function getDetailsAttribute(){
        return $this->file->details  ;
        return [
            'id'=>$this->id ,
            'file'=>$this->file->details 

        ] ;
    }


}