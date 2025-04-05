<?php

namespace App\Http\Controllers\Panel\Store;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize("panel_products_comments");

        $user = auth()->user();

        $query = Comment::where('status', 'active')
            ->whereNotNull('product_id')
            ->whereHas('product', function ($query) use ($user) {
                $query->where('creator_id', $user->id);
            })
            ->with([
                'product' => function ($query) {
                    $query->select('id', 'slug');
                },
                'user' => function ($qu) {
                    $qu->select('id', 'full_name', 'avatar');
                },
                'replies'
            ]);


        $repliedCommentsCount = deepClone($query)->whereNotNull('reply_id')->count();

        $query = $this->filterComments($query, $request);

        $comments = $query->orderBy('created_at', 'desc')
            ->paginate(10);


        foreach ($comments->whereNull('viewed_at') as $comment) {
            $comment->update([
                'viewed_at' => time()
            ]);
        }

        $data = [
            'pageTitle' => trans('panel.my_class_comments'),
            'comments' => $comments,
            'repliedCommentsCount' => $repliedCommentsCount,
        ];

        return view('web.default.panel.store.comments', $data);
    }

    public function myComments(Request $request)
    {
        $this->authorize("panel_products_my_comments");

        $user = auth()->user();

        $query = Comment::where('user_id', $user->id)
            ->whereNotNull('product_id')
            ->with([
                'product' => function ($query) {
                    $query->select('id', 'slug');
                }
            ]);

        $query = $this->filterComments($query, $request);

        $comments = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('panel.my_comments'),
            'comments' => $comments,
        ];

        return view('web.default.panel.store.my_comments', $data);
    }

    private function filterComments($query, $request)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $user = $request->get('user', null);
        $product = $request->get('product', null);

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

        if (!empty($product)) {
            $productsIds = Product::whereTranslationLike('title', "%$product%")->pluck('id')->toArray();

            $query->whereIn('product_id', $productsIds);
        }

        return $query;
    }
}
