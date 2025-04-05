<?php

namespace App\Models\Api;

use App\Models\RewardAccounting as Model;

class RewardAccounting extends Model
{
    //
    public function getDetailsAttribute()
    {
        return [
            'id' => $this->id,
            'user'=>$this->user->brief,
            'item_id'=>$this->item_id,
            'type'=>$this->type ,
            'score'=>$this->score,
            'status'=>($this->status==self::ADDICTION)?'addition':$this->status ,
            'created_at'=>$this->created_at,

        ];
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Api\User', 'user_id', 'id');
    }
}
