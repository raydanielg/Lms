<?php
namespace App\Models\Api;

use App\Models\WebinarReview as Model;

class WebinarReview extends Model
{
    public function getDetailsAttribute()
    {
        return [
            'id'=>$this->id ,
            'auth' => $this->auth,
            'user' => [
                'full_name' => $this->creator->full_name,
                'avatar' => url($this->creator->getAvatar()),
            ],
            'created_at' => $this->created_at,
            'description' => $this->description,
            'rate' => $this->rates,
            'rate_type' => [
                'content_quality' => $this->content_quality,
                'instructor_skills' => $this->instructor_skills,
                'purchase_worth' => $this->purchase_worth,
                'support_quality' => $this->support_quality,
            ],
            'replies' => $this->comments->where('status', 'active')->map(function ($reply) {
                return [
                    'id' => $this->id,
                    'user' => [
                        'full_name' => $reply->user->full_name,
                        'avatar' => url($reply->user->getAvatar()),
                    ],
                    'created_at' => $reply->created_at,
                    'comment' => $reply->comment,
                ];

            })
        ];
    }

    public function getAuthAttribute()
    {
        $user = apiAuth();
        if (!$user) {
            return null;
        }
        if ($user->id == $this->creator_id) {
            return true;
        }
        return false;
    }

    public function webinar()
    {
        return $this->belongsTo('App\Models\Api\Webinar', 'webinar_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo('App\Models\Api\User', 'creator_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Api\Comment', 'review_id', 'id');
    }

}

?>
