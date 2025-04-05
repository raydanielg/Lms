<?php

namespace App\Models\Api;
use App\Models\Faq as Model ;

class Faq extends Model
{
   public function  getDetailsAttribute(){
        return [
            'id'=>$this->id ,
            'title'=>$this->title ,
            'answer'=>$this->answer ,
            'order'=>$this->order ,
            'created_at'=>$this->created_at ,
            'updated_at'=>$this->updated_at
        ] ;
    }
}
