<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function myClassComments(Request $request)
    {
        $user = auth()->user();

        $query = Comment::where('status', 'active')
            ->whereHas('webinar', function ($query) use ($user) {
                $query->where(function ($query) use ($user) {
                    $query->where('creator_id', $user->id);
                    $query->orWhere('teacher_id', $user->id);
                });
            })
            ->with([
                'webinar' => function ($query) {
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

        return view(getTemplate() . '.panel.webinar.comments', $data);
    }

    public function myComments(Request $request)
    {
        $user = auth()->user();

        $query = Comment::where('user_id', $user->id)
            ->whereNotNull('webinar_id')
            ->with(['webinar' => function ($query) {
                $query->select('id', 'slug');
            }]);

        $query = $this->filterComments($query, $request);

        $comments = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('panel.my_comments'),
            'comments' => $comments,
        ];

        return view(getTemplate() . '.panel.webinar.my_comments', $data);
    }

    private function filterComments($query, $request)
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
            $webinarsIds = Webinar::whereTranslationLike('title', "%$webinar%")->pluck('id')->toArray();

            $query->whereIn('webinar_id', $webinarsIds);
        }

        if (!empty($filter_new_comments) and $filter_new_comments == 'on') {

        }

        return $query;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'webinar_id' => 'required_without:product_id',
            'product_id' => 'required_without:webinar_id',
            'comment' => 'nullable',
        ]);

        Comment::create([
            'webinar_id' => $request->input('webinar_id'),
            'product_id' => $request->input('product_id'),
            'user_id' => $request->input('user_id'),
            'comment' => $request->input('comment'),
            'reply_id' => $request->input('reply_id'),
            'status' => 'pending',
            'created_at' => time()
        ]);

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'comment' => 'required',
        ]);

        $user = auth()->user();

        $comment = Comment::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!empty($comment)) {
            $comment->update([
                'comment' => $request->input('comment'),
                'status' => 'pending',
            ]);
        }

        return response()->json([
            'code' => 200,
            'msg' => trans('product.comment_success_store')
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $user = auth()->user();

        $comment = Comment::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!empty($comment)) {
            $comment->delete();
        }

        return response()->json([
            'code' => 200
        ], 200);
    }

    public function reply(Request $request, $id)
    {
        $this->validate($request, [
            'comment' => 'required|string'
        ]);

        $user = auth()->user();

        $comment = Comment::where('id', $id)
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
            })->first();

        if (!empty($comment)) {

            Comment::create([
                'user_id' => $user->id,
                'comment' => $request->get('comment'),
                'webinar_id' => $comment->webinar_id,
                'product_id' => $comment->product_id,
                'reply_id' => $comment->id,
                'status' => 'active',
                'created_at' => time()
            ]);
        }

        return response()->json([
            'code' => 200,
            'msg' => trans('product.comment_success_store')
        ], 200);
    }

    public function report(Request $request, $id)
    {
        $this->validate($request, [
            'message' => 'required|string'
        ]);

        $data = $request->all();
        $user = auth()->user();

        $comment = Comment::where('id', $id)
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
            })->first();

        if (!empty($comment)) {

            CommentReport::create([
                'webinar_id' => $comment->webinar_id,
                'product_id' => $comment->product_id,
                'user_id' => $user->id,
                'comment_id' => $comment->id,
                'message' => $data['message'],
                'created_at' => time()
            ]);

            $notifyOptions = [
                '[u.name]' => $user->full_name,
                '[content_type]' => trans('admin/main.comment')
            ];
            sendNotification("new_report_item_for_admin", $notifyOptions, 1);

            return response()->json([
                'code' => 200
            ], 200);
        }

        return response()->json([], 422);
    }
}
