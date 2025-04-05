<?php
namespace App\Models\Api;

use App\Http\Resources\ProductResource;
use App\Models\Comment as Model;
use App\Models\Api\Product;

class Comment extends Model
{

    public function getDetailsAttribute()
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'comment_user_type' => $this->comment_user_type,
            'create_at' => $this->created_at,
            'comment' => $this->comment,
            'blog' => $this->blog->brief ?? null,
            'user' => $this->user->brief ?? null,
            'webinar' => $this->webinar->brief ?? null,
            'product' => $this->product ? new ProductResource($this->product) : null,
            'replies' => $this->replies->where('status', 'active')->map(function ($reply) {
                return [
                    'id' => $reply->id,
                    'comment_user_type' => $reply->comment_user_type,
                    'user' => $reply->user->brief,
                    'create_at' => $reply->created_at,
                    'comment' => $reply->comment,
                ];

            })
        ];
    }

    public function scopeHandleFilters($query)
    {
        $request = request();
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $user = $request->get('user_id', null);
        $webinar = $request->get('webinar_id', null);
        $product = $request->get('product_id', null);
        $blogId = $request->get('blog_id', null);

        $filter_new_comments = request()->get('new_comments', null);

        if (!empty($from) and !empty($to)) {
            $from = strtotime($from);
            $to = strtotime($to);

            $query->whereBetween('created_at', [$from, $to]);
        } else {
            if (!empty($from)) {
                $from = strtotime($from);

                $query->where('created_at', '>=', $from);
            }

            if (!empty($to)) {
                $to = strtotime($to);

                $query->where('created_at', '<', $to);
            }
        }

        if (!empty($user)) {
            $usersIds = User::where('full_name', 'like', "%$user%")->pluck('id')->toArray();

            $query->whereIn('user_id', $usersIds);
        }

        if (!empty($webinar)) {
            $webinarsIds = Webinar::where('title', 'like', "%$webinar%")->pluck('id')->toArray();

            $query->whereIn('webinar_id', $webinarsIds);
        }

        if (!empty($filter_new_comments) and $filter_new_comments == 'on') {

        }
        if (!empty($product)) {
            $productsIds = Product::whereTranslationLike('title', "%$product%")->pluck('id')->toArray();

            $query->whereIn('product_id', $productsIds);
        }

        if (!empty($blogId) and is_numeric($blogId)) {
            $query->where('blog_id', $blogId);
        }

        return $query;
    }

    public function getCommentUserTypeAttribute()
    {

        if ($this->user->isUser() or
            !empty($this->webinar) and
            $this->webinar->checkUserHasBought($this->user)) {
            $type = 'student';
        } elseif (
            !$this->user->isUser()
            and !empty($this->webinar)
            and ($this->webinar->creator_id == $this->user->id or
                $this->webinar->teacher_id == $this->user->id)

        ) {
            $type = 'teacher';
        } elseif ($this->user->isAdmin()) {
            $type = 'staff';
        } else {
            $type = 'user';
        }
        return $type;

    }


    public function replies()
    {
        return $this->hasMany($this, 'reply_id', 'id');
    }

    public function webinar()
    {
        return $this->belongsTo('App\Models\Api\Webinar', 'webinar_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Api\User', 'user_id', 'id');
    }

    public function review()
    {
        return $this->belongsTo('App\Models\Api\WebinarReview', 'review_id', 'id');
    }

    public function blog()
    {
        return $this->belongsTo('App\Models\Api\Blog', 'blog_id', 'id');
    }

}

?>
