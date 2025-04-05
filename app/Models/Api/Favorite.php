<?php
namespace App\Models\Api ;
use App\Models\Favorite as WebFavorite;

class Favorite extends WebFavorite{

    public function webinar()
    {
        return $this->belongsTo('App\Models\Api\Webinar', 'webinar_id', 'id');
    }
    
    public function user()
    {
        return $this->belongsTo('App\Models\Api\User', 'user_id', 'id');
    }
}