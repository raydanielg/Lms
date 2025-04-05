<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Reward;
use App\Models\RewardAccounting;
use Illuminate\Http\Request;

class ReviewsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_store_products_reviews');

        $query = ProductReview::query();

        $totalReviews = deepClone($query)->count();
        $publishedReviews = deepClone($query)->where('status', 'active')->count();
        $ratesAverage = deepClone($query)->avg('rates');
        $productsWithoutReview = Product::where('status', Product::$active)->whereDoesntHave('reviews')->count();

        $query = $this->filters($query, $request);

        $reviews = $query->orderBy('created_at', 'desc')
            ->with([
                'product' => function ($query) {
                    $query->select('id', 'slug');
                },
                'creator' => function ($query) {
                    $query->select('id', 'full_name');
                },
            ])
            ->withCount('comments')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.admin_store_reviews_list_title'),
            'totalReviews' => $totalReviews,
            'publishedReviews' => $publishedReviews,
            'ratesAverage' => round($ratesAverage, 2),
            'productsWithoutReview' => $productsWithoutReview,
            'reviews' => $reviews,
        ];

        $product_ids = $request->get('product_ids');
        if (!empty($product_ids)) {
            $data['products'] = Product::select('id')->whereIn('id', $product_ids)->get();
        }

        return view('admin.store.reviews.lists', $data);
    }

    private function filters($query, $request)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $search = $request->get('search', null);
        $product_ids = $request->get('product_ids');
        $status = $request->get('status', null);

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($search)) {
            $query->where('description', 'like', "%$search%");
        }

        if (!empty($product_ids)) {
            $query->whereIn('product_id', $product_ids);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function toggleStatus($id)
    {
        $this->authorize('admin_store_products_reviews_status_toggle');

        $review = ProductReview::findOrFail($id);

        $review->update([
            'status' => ($review->status == 'active') ? 'pending' : 'active',
        ]);

        /*if ($review->status == 'active') {
            $reviewReward = RewardAccounting::calculateScore(Reward::REVIEW_COURSES);
            RewardAccounting::makeRewardAccounting($review->creator_id, $reviewReward, Reward::REVIEW_COURSES, $review->id, true);
        }*/

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => 'Review status changed successful',
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }

    public function reply(Request $request, $id)
    {
        $this->authorize('admin_reviews_reply');

        $review = ProductReview::findOrFail($id);

        $data = [
            'pageTitle' => trans('admin/pages/comments.reply_comment'),
            'review' => $review,
        ];

        return view('admin.store.reviews.comment_reply', $data);
    }

    public function delete($id)
    {
        $this->authorize('admin_store_products_reviews_delete');

        $review = ProductReview::findOrFail($id);

        $review->delete();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => 'Review deleted successful',
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }
}
