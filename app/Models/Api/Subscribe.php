<?php

namespace App\Models\Api;

use App\Models\Subscribe as Model;

class Subscribe extends Model
{
    //
    public function getDetailsAttribute(){
        return [
            'id'=>$this->id ,
            'title'=>$this->title ,
            'description'=>$this->description ,
            'usable_count'=>$this->usable_count ,
            'days'=>$this->days ,
            'price'=>$this->price ,
            'is_popular'=>$this->is_popular ,
            'image'=>($this->icon)?url($this->icon):null ,
            'created_at'=>$this->created_at ,
        ] ;
    }
}
