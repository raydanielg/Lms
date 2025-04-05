<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Controller;
use App\Http\Controllers\Api\Objects\BlogObj;
use App\Http\Controllers\Api\Objects\UserObj;
use App\Http\Controllers\Api\Objects\WebinarObj;
use App\Models\Api\Comment;
use App\Models\CommentReport;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommentsController extends Controller
{
    public function list(Request $request)
    {
        $data = [
            'my_comment' => $this->myComments($request),
            'class_comment' => $this->myClassComments($request),
        ];
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $data);
    }

    public function myClassComments(Request $request)
    {
        $user = apiAuth();

        $comments = Comment::where('status', 'active')
            ->whereHas('webinar', function ($query) use ($user) {
                $query->where(function ($query) use ($user) {
                    $query->where('creator_id', $user->id);
                    $query->orWhere('teacher_id', $user->id);
                });
            })->handleFilters()->orderBy('created_at', 'desc')
            ->get();


        foreach ($comments->whereNull('viewed_at') as $comment) {
            $comment->update([
                'viewed_at' => time()
            ]);
        }
        $comments = $comments->map(function ($comment) {
            return $comment->details;
        });
        return $comments;

    }

    public function myComments(Request $request)
    {
        $user = apiAuth();

        $query = Comment::where('user_id', $user->id);

        $webinar_query = clone $query;
        $webinar_comments = $webinar_query->whereNotNull('webinar_id')
            ->handleFilters()->orderBy('created_at', 'desc')
            ->get()->map(function ($comment) {
                return $comment->details;
            });;


        $blog_comments = clone $query;
        $blog_comments = $blog_comments->whereNotNull('blog_id')
            ->handleFilters()->orderBy('created_at', 'desc')
            ->get()->map(function ($comment) {
                return $comment->details;
            });


        return ['blogs' => $blog_comments, 'webinar' => $webinar_comments];

    }

    private function handleFilter($query, $request)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $user = $request->get('user', null);
        $webinar = $request->get('webinar', null);
        $filter_new_comments = request()->get('new_comments', null);

        fromAndToDateFilter($from, $to, $query, 'created_at');

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

        return $query;
    }

    public function store(Request $request)
    {
        $rules = [
            'item_id' => 'required',
            'item_name' => ['required', Rule::in(['blog', 'webinar', 'product', 'bundle'])],
            'comment' => 'required|string',
        ];


        $item_name = $request->input('item_name');
        $item_id = $request->input('item_id');

        if ($item_name == 'webinar') {
            $rules['item_id'] = 'required|exists:webinars,id';
        } elseif ($item_name == 'blog') {
            $rules['item_id'] = 'required|exists:blog,id';
        } elseif ($item_name == 'product') {
            $rules['item_id'] = 'required|exists:products,id';
        } elseif ($item_name == 'bundle') {
            $rules['item_id'] = 'required|exists:bundles,id';
        }
        validateParam($request->all(), $rules);


        $user = apiAuth();
        $item_name = $item_name . '_id';
        $status = self::getStatus();
        $comment = Comment::create([
            $item_name => $item_id,
            'user_id' => $user->id,
            'comment' => $request->input('comment'),
            'reply_id' => $request->input('reply_id'),
            'status' => $status,
            'created_at' => time()
        ]);

        if ($item_name == 'webinar_id') {
            if ($status !== Comment::$active) {
                $webinar = Webinar::FindOrFail($item_id);
                $notifyOptions = [
                    '[c.title]' => $webinar->title,
                    '[u.name]' => $user->full_name
                ];
                sendNotification('new_comment', $notifyOptions, 1);
            }
        } elseif ($item_name == 'product_id') {
            if ($status !== Comment::$active) {
                $product = $comment->product;
                $notifyOptions = [
                    '[p.title]' => $product->title,
                    '[u.name]' => $user->full_name
                ];
                sendNotification('product_new_comment', $notifyOptions, 1);
            }
        } elseif ($item_name == 'blog_id') {
            $blog = $comment->blog;

            if (!empty($blog) and !$blog->author->isAdmin()) {
                if ($status !== Comment::$active) {
                    $notifyOptions = [
                        '[blog_title]' => $blog->title,
                        '[u.name]' => $user->full_name
                    ];
                    sendNotification('new_comment_for_instructor_blog_post', $notifyOptions, $blog->author->id);
                }

                $buyStoreReward = RewardAccounting::calculateScore(Reward::COMMENT_FOR_INSTRUCTOR_BLOG);
                RewardAccounting::makeRewardAccounting($comment->user_id, $buyStoreReward, Reward::COMMENT_FOR_INSTRUCTOR_BLOG, $comment->id);
            }
        }

        return apiResponse2(1, 'stored',
            trans('product.comment_success_store_msg'),
            ["status"=>$status],
            trans('product.comment_success_store')

        );
    }

    public function update(Request $request, $id)
    {
        validateParam($request->all(), [
            'comment' => 'required',
        ]);

        $user = apiAuth();

        $comment = Comment::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (empty($comment)) {
            abort(404);
        }
        $comment->update([
            'comment' => $request->input('comment'),
            'status' => self::getStatus(),
        ]);
        return apiResponse2(1, 'updated', trans('api.public.updated'));

    }

    public function destroy(Request $request, $id)
    {
        $user = apiAuth();
        $comment = Comment::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        if (!$comment) {
            abort(404);
        }
        $comment->delete();

        return apiResponse2(1, 'deleted', trans('api.public.deleted'));


    }

    public function reply(Request $request, $id)
    {
        validateParam($request->all(), [
            'reply' => 'required|string'
        ]);

        $user = apiAuth();


        $comment = Comment::where('id', $id)->first();
        if ($comment->webinar_id) {
            $item_name = 'webinar_id';
        } elseif ($comment->blog_id) {
            $item_name = 'blog_id';
        } elseif ($comment->bundle_id) {
            $item_name = 'bundle_id';
        } elseif ($comment->product_id) {
            $item_name = 'product_id';
        }
        /* $comment = Comment::where('id', $id)
             ->where(function ($query) use ($user) {
                 $query->where('user_id', $user->id);
                 $query->orWhereHas('webinar', function ($query) use ($user) {
                     $query->where(function ($query) use ($user) {
                         $query->where('creator_id', $user->id);
                         $query->orWhere('teacher_id', $user->id);
                     });
                 });
                 $query->orWhereHas('product', function ($query) use ($user) {
                     $query->where('creator_id', $user->id);
                 });
             })->first();*/

        if (!$comment) {
            abort(404);
        }
        $status = self::getStatus();

        if ($comment->webinar->creator_id ?? null == $user->id or $comment->product->creator_id ?? null == $user->id) {
            $status = 'active';
        }

        Comment::create([
            'user_id' => $user->id,
            $item_name => $id,
            'comment' => $request->get('reply'),
            'webinar_id' => $comment->webinar_id,
            'product_id' => $comment->product_id,
            'reply_id' => $comment->id,
            'status' => $status,
            'created_at' => time()
        ]);

        return apiResponse2(1, 'stored', trans('api.public.stored'));


    }

    public function report(Request $request, $id)
    {
        validateParam($request->all(), [
            'message' => 'required|string'
        ]);

        $user = apiAuth();
        $userWebinarsIds = $user->webinars->pluck('id')->toArray();

        $comment = Comment::where('id', $id)
            /*  ->where(function ($query) use ($user, $userWebinarsIds) {
                  $query->where('user_id', $user)
                      ->orWhereIn('webinar_id', $userWebinarsIds);})*/
            ->first();
        if (!$comment) {
            abort(404);
        }
        $item_name = null;
        if ($comment->webinar_id) {
            $idd= $comment->webinar_id;
            $item_name = 'webinar_id';
        } elseif ($comment->blog_id) {
            $idd= $comment->blog_id;
            $item_name = 'blog_id';
        } elseif ($comment->bundle_id) {
            $idd= $comment->bundle_id;
            $item_name = 'bundle_id';
        } elseif ($comment->product_id) {
            $idd= $comment->product_id;
            $item_name = 'product_id';
        }

        CommentReport::create([
            $item_name => $idd,
            'user_id' => $user->id,
            'comment_id' => $comment->id,
            'message' => $request->input('message'),
            'created_at' => time()
        ]);

        return apiResponse2(1, 'stored', trans('panel.report_success'));

    }
    public static function getStatus()
    {
        $status = Comment::$pending;
        if (!empty(getGeneralOptionsSettings('direct_publication_of_comments'))) {
            $status = Comment::$active;
        }

        return $status;
    }
}









