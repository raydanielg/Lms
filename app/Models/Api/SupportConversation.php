<?php
namespace App\Models\Api ;
use App\Models\SupportConversation as Model ;

class SupportConversation extends Model{

    public function getBriefAttribute(){

        return [
            'message' => $this->message,
            'sender' =>($this->sender_id) ?[
                'id' => $this->sender->id,
                'full_name' => $this->sender->full_name,
                'avatar' => url($this->sender->getAvatar()),
            ]:null ,
            'supporter' => ($this->supporter_id) ? [
                'id' => $this->supporter->id,
                'full_name' => $this->supporter->full_name,
                'avatar' => url($this->supporter->getAvatar()),
            ] : null,
        
            'attach'=>($this->attach)?url($this->attach):null ,
            'created_at' => $this->created_at ,
           
        ];

    }
}