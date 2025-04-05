<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Api\WebinarReview;
use App\Models\Blog;
use App\Models\Bundle;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class CommentsController extends Controller
{
    public $page = '';
    public $item = '';
    public $item_column = '';

    public function __construct(Request $request)
    {
        $page = Route::current()->parameter('page');
        $this->page = $page;

        if ($page == 'webinars') {
            $this->item = 'webinar';
            $this->item_column = 'webinar_id';
        } else if ($page == 'bundles') {
            $this->item = 'bundle';
            $this->item_column = 'bundle_id';
        } else if ($page == 'products') {
            $this->item = 'product';
            $this->item_column = 'product_id';
        } else if ($page == 'reviews') {
            $this->item = 'review';
            $this->item_column = 'review_id';
        } else if ($page == 'product_reviews') {
            $this->item = 'product_review';
            $this->item_column = 'product_review_id';
        } else {
            $this->item = 'blog';
            $this->item_column = 'blog_id';
        }
    }

    public function index(Request $request)
    {
        $this->authorize('admin_' . $this->item . '_comments');

        $query = Comment::whereNotNull($this->item_column);

        $totalComments = deepClone($query)->count();
        $publishedComments = deepClone($query)->where('status', 'active')->count();
        $pendingComments = deepClone($query)->where('status', 'pending')->count();
        $commentReports = CommentReport::whereNotNull($this->item_column)->count();

        $query = $this->filters($query, $request);
        $comments = $query->with([
            $this->item, // webinar or blog relation
            'user' => function ($query) {
                $query->select('id', 'full_name');
            }
        ])->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'itemRelation' => $this->item,
            'page' => $this->page,
            'pageTitle' => trans("update.admin_{$this->page}_comments_page_title"),
            'comments' => $comments,
            'totalComments' => $totalComments,
            'publishedComments' => $publishedComments,
            'pendingComments' => $pendingComments,
            'commentReports' => $commentReports,
        ];

        $user_ids = $request->get('user_ids');
        $webinar_ids = $request->get('webinar_ids');
        $post_ids = $request->get('post_ids');
        $product_ids = $request->get('product_ids');
        $bundle_ids = $request->get('bundle_ids');

        if (!empty($user_ids)) {
            $data['users'] = User::select('id', 'full_name')->whereIn('id', $user_ids)->get();
        }

        if (!empty($webinar_ids)) {
            $data['webinars'] = Webinar::select('id')->whereIn('id', $webinar_ids)->get();
        }

        if (!empty($post_ids)) {
            $data['blog'] = Blog::select('id')->whereIn('id', $post_ids)->get();
        }

        if (!empty($product_ids)) {
            $data['products'] = Product::select('id')->whereIn('id', $product_ids)->get();
        }

        if (!empty($bundle_ids)) {
            $data['bundles'] = Bundle::select('id')->whereIn('id', $bundle_ids)->get();
        }

        return view('admin.comments.comments', $data);
    }

    private function filters($query, $request)
    {
        $title = $request->get('title', null);
        $date = $request->get('date', null);
        $webinar_ids = $request->get('webinar_ids', null);
        $user_ids = $request->get('user_ids', null);
        $post_ids = $request->get('post_ids', null);
        $product_ids = $request->get('product_ids', null);
        $bundle_ids = $request->get('bundle_ids');
        $status = $request->get('status', null);

        if (!empty($title)) {
            $query->where('title', 'like', "%$title%");
        }

        if (!empty($date)) {
            $timestamp = strtotime($date);
            $beginOfDay = strtotime("today", $timestamp);
            $endOfDay = strtotime("tomorrow", $beginOfDay) - 1;

            $query->whereBetween('created_at', [$beginOfDay, $endOfDay]);
        }

        if (!empty($webinar_ids)) {
            $query->whereIn('webinar_id', $webinar_ids);
        }

        if (!empty($user_ids)) {
            $query->whereIn('user_id', $user_ids);
        }

        if (!empty($post_ids)) {
            $query->whereIn('blog_id', $post_ids);
        }

        if (!empty($product_ids)) {
            $query->whereIn('product_id', $product_ids);
        }

        if (!empty($bundle_ids)) {
            $query->whereIn('bundle_id', $bundle_ids);
        }

        if (!empty($status) and in_array($status, ['active', 'pending'])) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function toggleStatus($page, $comment_id)
    {
        $this->authorize('admin_comments_status');

        $comment = Comment::where('id', $comment_id)
            ->whereNotNull($this->item_column)
            ->first();

        if (!empty($comment)) {
            $comment->update([
                'status' => ($comment->status == 'pending') ? 'active' : 'pending',
            ]);

            if ($comment->status == 'active') {
                $commentReward = RewardAccounting::calculateScore(Reward::COMMENT);
                RewardAccounting::makeRewardAccounting($comment->user_id, $commentReward, Reward::COMMENT, $comment->id, true);

                // Blog Comment Reward
                if ($this->item == 'blog') {
                    $blogCommentReward = RewardAccounting::calculateScore(Reward::COMMENT_FOR_INSTRUCTOR_BLOG);
                    RewardAccounting::makeRewardAccounting($comment->user_id, $blogCommentReward, Reward::COMMENT_FOR_INSTRUCTOR_BLOG, $comment->id, true);
                }
            }

            if ($comment->status == 'active' and !empty($comment->webinar_id)) {
                $webinar = Webinar::FindOrFail($comment->webinar_id);
                $commentedUser = User::findOrFail($comment->user_id);

                $notifyOptions = [
                    '[c.title]' => $webinar->title,
                    '[u.name]' => $commentedUser->full_name
                ];
                sendNotification('new_comment', $notifyOptions, $webinar->teacher_id);
            } elseif ($comment->status == 'active' and !empty($comment->product_id)) {
                $product = $comment->product;
                $commentedUser = $comment->user;

                $notifyOptions = [
                    '[p.title]' => $product->title,
                    '[u.name]' => $commentedUser->full_name
                ];
                sendNotification('product_new_comment', $notifyOptions, $product->creator_id);
            }
        }

        return redirect()->back();
    }

    public function edit($page, $comment_id)
    {
        $this->authorize('admin_comments_edit');

        $comment = Comment::where('id', $comment_id)
            ->whereNotNull($this->item_column)
            ->first();

        if (!empty($comment)) {
            $data = [
                'pageTitle' => trans('admin/pages/comments.edit_comment'),
                'itemRelation' => $this->item,
                'page' => $this->page,
                'comment' => $comment,
            ];

            return view('admin.comments.comment_edit', $data);
        }

        abort(404);
    }

    public function update(Request $request, $page, $comment_id)
    {
        $this->authorize('admin_comments_edit');

        $this->validate($request, [
            'comment' => 'required|string'
        ]);

        $comment = Comment::where('id', $comment_id)
            ->whereNotNull($this->item_column)
            ->first();

        if (!empty($comment)) {
            $comment->update([
                'comment' => $request->get('comment'),
            ]);
        }

        return redirect()->back();
    }

    public function reply($page, $comment_id)
    {
        $this->authorize('admin_comments_reply');

        $comment = Comment::where('id', $comment_id)
            ->whereNotNull($this->item_column)
            ->with('replies')
            ->first();

        if (!empty($comment)) {
            $data = [
                'pageTitle' => trans('admin/pages/comments.reply_comment'),
                'itemRelation' => $this->item,
                'page' => $this->page,
                'comment' => $comment,
            ];

            return view('admin.comments.comment_reply', $data);
        }

        abort(404);
    }

    public function storeReply(Request $request, $page, $comment_id)
    {
        $this->authorize('admin_comments_reply');

        $this->validate($request, [
            'comment' => 'required|string'
        ]);


        if ($this->item == "review") {
            $comment = WebinarReview::query()->where('id', $comment_id)->first();
        } else if ($this->item == "product_review") {
            $comment = ProductReview::query()->where('id', $comment_id)->first();
        } else {
            $comment = Comment::where('id', $comment_id)
                ->whereNotNull($this->item_column)
                ->first();
        }

        if (!empty($comment)) {

            Comment::create([
                'user_id' => auth()->user()->id,
                'comment' => $request->get('comment'),
                $this->item_column => in_array($this->item, ["review", "product_review"]) ? $comment->id : $comment->{$this->item_column},
                'reply_id' => !in_array($this->item, ["review", "product_review"]) ? $comment->id : null,
                'status' => 'active',
                'created_at' => time()
            ]);
        }

        return redirect()->back();
    }

    public function delete(Request $request, $page, $comment_id)
    {
        $this->authorize('admin_comments_delete');

        $comment = Comment::where('id', $comment_id)
            ->whereNotNull($this->item_column)
            ->first();

        if (!empty($comment)) {
            $comment->delete();
        }

        if (!empty($request->get('redirect_to'))) {
            return redirect($request->get('redirect_to'));
        }

        return redirect()->back();
    }

    public function reports()
    {
        $this->authorize('admin_comments_reports');

        $reports = CommentReport::whereNotNull($this->item_column)
            ->with([$this->item, 'user' => function ($query) {
                $query->select('id', 'full_name');
            }])->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/pages/comments.comments_reports'),
            'itemRelation' => $this->item,
            'page' => $this->page,
            'reports' => $reports,
        ];

        return view('admin.comments.reports', $data);
    }

    public function reportShow($page, $id)
    {
        $this->authorize('admin_comments_reports');

        $report = CommentReport::where('id', $id)
            ->whereNotNull($this->item_column)
            ->with(['comment', 'user' => function ($query) {
                $query->select('id', 'full_name');
            }])->first();

        if (!empty($report)) {
            $data = [
                'pageTitle' => trans('admin/pages/comments.comments_reports'),
                'itemRelation' => $this->item,
                'page' => $this->page,
                'report' => $report,
                'comment' => $report->comment
            ];

            return view('admin.comments.report_show', $data);
        }

        abort(404);
    }

    public function reportDelete(Request $request, $page, $id)
    {
        $this->authorize('admin_comments_reports');

        $report = CommentReport::where('id', $id)
            ->whereNotNull($this->item_column)
            ->first();

        if (!empty($report)) {
            $report->delete();
        }

        if (!empty($request->get('redirect_to'))) {
            return redirect($request->get('redirect_to'));
        }

        return redirect()->back();
    }
}
