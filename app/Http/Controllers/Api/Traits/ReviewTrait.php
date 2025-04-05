<?php

namespace App\Http\Controllers\Api\Traits;

use App\Models\Api\WebinarReview;
use App\Models\Bundle;
use App\Models\Comment;
use App\Models\Webinar;
use Illuminate\Http\Request;

trait ReviewTrait
{
    public function store(Request $request)
    {
        $rules = [
            'item' => 'required|in:bundle,webinar',
            'id' => 'required',
            'content_quality' => 'required',
            'instructor_skills' => 'required',
            'purchase_worth' => 'required',
            'support_quality' => 'required',
            'description' => 'required',
        ];
        validateParam($request->all(), $rules);
        $user = apiAuth();
        $item = $request->input('item');
        $id = $request->input('id');
        if ($item == 'webinar') {
            $itemObj = Webinar::where('id', $id)
                ->where('status', 'active')
                ->first();
        } elseif ($item == 'bundle') {
            $itemObj = Bundle::where('id', $id)
                ->where('status', 'active')
                ->first();
        } elseif ($item == 'product') {

        }

        if (!$itemObj) {
            abort(404);
        }

        if (!$itemObj->checkUserHasBought($user)) {
            return apiResponse2(0, 'not_purchased',
                trans('cart.you_not_purchased_this_course')
            );
        }

        $webinarReview = WebinarReview::where('creator_id', $user->id)
            ->where($item . '_id', $itemObj->id)
            ->first();

        if (!empty($webinarReview)) {
            return apiResponse2(0, 'already_sent',
                trans('public.duplicate_review_for_webinar')

            );
        }

        $rates = 0;
        $rates += (int)$request->input('content_quality');
        $rates += (int)$request->input('instructor_skills');
        $rates += (int)$request->input('purchase_worth');
        $rates += (int)$request->input('support_quality');

        WebinarReview::create([
            $item . '_id' => $itemObj->id,
            'creator_id' => $user->id,
            'content_quality' => (int)$request->input('content_quality'),
            'instructor_skills' => (int)$request->input('instructor_skills'),
            'purchase_worth' => (int)$request->input('purchase_worth'),
            'support_quality' => (int)$request->input('support_quality'),
            'rates' => $rates > 0 ? $rates / 4 : 0,
            'description' => $request->input('description'),
            'created_at' => time(),
        ]);

        return apiResponse2(1, 'stored',
            trans('webinars.your_reviews_successfully_submitted_and_waiting_for_admin'),

        );

    }

    public function reply(Request $request, $id)
    {
        validateParam($request->all(), [
            'reply' => 'required'
        ]);
        if (empty(WebinarReview::find($id))) {
            abort(404);
        }

        Comment::create([
            'user_id' => apiAuth()->id,
            'comment' => $request->input('reply'),
            'review_id' => $id,
            'status' => $request->input('status') ?? Comment::$pending,
            'created_at' => time()
        ]);

        return apiResponse2(1, 'stored', trans('api.public.stored'));

    }

    public function destroy(Request $request, $id)
    {
        $user = apiAuth();
        validateParam($request->all(), [
            'item' => 'in:bundle,webinar'
        ]);

        $review = WebinarReview::where('id', $id)
            ->where('creator_id', $user->id)
            ->first();
        if (!$review) {
            abort(404);
        }
        $review->delete();
        return apiResponse2(1, 'deleted', trans('webinars.your_review_deleted')
        );

    }
}
