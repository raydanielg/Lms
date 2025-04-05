<?php
namespace App\Models\Api ;

use App\Models\SupportDepartment as Model ;

class SupportDepartment extends Model {

    public function getDetailsAttribute(){
        return [
            'id'=>$this->id ,
            'title'=>$this->title ,
        ] ;
    }
}