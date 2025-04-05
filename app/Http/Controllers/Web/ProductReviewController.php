<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required',
            'product_quality' => 'required',
            'purchase_worth' => 'required',
            'delivery_quality' => 'required',
            'seller_quality' => 'required',
        ]);

        $data = $request->all();
        $user = auth()->user();

        $product = Product::where('id', $data['product_id'])
            ->where('status', 'active')
            ->first();

        if (!empty($product)) {
            if ($product->checkUserHasBought($user)) {
                $productReview = ProductReview::where('creator_id', $user->id)
                    ->where('product_id', $product->id)
                    ->first();

                if (!empty($productReview)) {
                    $toastData = [
                        'title' => trans('public.request_failed'),
                        'msg' => trans('update.duplicate_review_for_product'),
                        'status' => 'error'
                    ];
                    return back()->with(['toast' => $toastData]);
                }

                $rates = 0;
                $rates += (int)$data['product_quality'];
                $rates += (int)$data['purchase_worth'];
                $rates += (int)$data['delivery_quality'];
                $rates += (int)$data['seller_quality'];

                ProductReview::create([
                    'product_id' => $product->id,
                    'creator_id' => $user->id,
                    'product_quality' => (int)$data['product_quality'],
                    'purchase_worth' => (int)$data['purchase_worth'],
                    'delivery_quality' => (int)$data['delivery_quality'],
                    'seller_quality' => (int)$data['seller_quality'],
                    'rates' => $rates > 0 ? $rates / 4 : 0,
                    'description' => $data['description'],
                    'status' => 'pending',
                    'created_at' => time(),
                ]);

                $notifyOptions = [
                    '[p.title]' => $product->title,
                    '[u.name]' => $user->full_name,
                    '[item_title]' => $product->title,
                    '[content_type]' => trans('update.product'),
                    '[rate.count]' => $rates > 0 ? $rates / 4 : 0,
                ];
                sendNotification('product_new_rating', $notifyOptions, $product->creator_id);
                sendNotification('new_user_item_rating', $notifyOptions, 1);

                $toastData = [
                    'title' => trans('public.request_success'),
                    'msg' => trans('webinars.your_reviews_successfully_submitted_and_waiting_for_admin'),
                    'status' => 'success'
                ];
                return back()->with(['toast' => $toastData]);
            } else {
                $toastData = [
                    'title' => trans('public.request_failed'),
                    'msg' => trans('update.you_not_purchased_this_product'),
                    'status' => 'error'
                ];
                return back()->with(['toast' => $toastData]);
            }
        }

        $toastData = [
            'title' => trans('public.request_failed'),
            'msg' => trans('cart.course_not_found'),
            'status' => 'error'
        ];
        return back()->with(['toast' => $toastData]);
    }

    public function storeReplyComment(Request $request)
    {
        $this->validate($request, [
            'reply' => 'nullable',
        ]);

        Comment::create([
            'user_id' => auth()->user()->id,
            'comment' => $request->input('reply'),
            'product_review_id' => $request->input('comment_id'),
            'status' => $request->input('status') ?? Comment::$pending,
            'created_at' => time()
        ]);

        $toastData = [
            'title' => trans('product.comment_success_store'),
            'msg' => trans('product.comment_success_store_msg'),
            'status' => 'success'
        ];
        return redirect()->back()->with(['toast' => $toastData]);
    }

    public function destroy(Request $request, $id)
    {
        if (auth()->check()) {
            $review = ProductReview::where('id', $id)
                ->where('creator_id', auth()->id())
                ->first();

            if (!empty($review)) {
                $review->delete();

                $toastData = [
                    'title' => trans('public.request_success'),
                    'msg' => trans('webinars.your_review_deleted'),
                    'status' => 'success'
                ];
                return back()->with(['toast' => $toastData]);
            }

            $toastData = [
                'title' => trans('public.request_failed'),
                'msg' => trans('webinars.you_not_access_review'),
                'status' => 'error'
            ];
            return back()->with(['toast' => $toastData]);
        }

        abort(404);
    }
}
