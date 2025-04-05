<?php

namespace App\Models\Api;

use App\Models\GroupUser as Model;

class GroupUser extends Model
{
    //

    public function getBriefAttribute(){

        if(!$this->group){
            return null ;
        }
        return [
            'id'=>$this->group->id ,
            'name'=>$this->group->name ,
            'status'=>$this->group->status ,
            'commission'=>$this->group->commission ,
            'discount'=>$this->group->discount 
        ] ;
    }
}
