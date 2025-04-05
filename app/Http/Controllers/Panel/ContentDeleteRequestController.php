<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Bundle;
use App\Models\ContentDeleteRequest;
use App\Models\Product;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContentDeleteRequestController extends Controller
{

    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'item_id' => 'required',
            'item_type' => 'required',
            'description' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $itemId = $data['item_id'];
        $itemType = $data['item_type'];

        if ($this->checkCanAccess($itemId, $itemType)) {

            $deleteRequest = ContentDeleteRequest::query()->updateOrCreate([
                'user_id' => $user->id,
                'targetable_id' => $itemId,
                'targetable_type' => $itemType,
            ], [
                'description' => $data['description'],
                'status' => 'pending',
                'created_at' => time(),
            ]);

            $contentItem = $deleteRequest->getContentItem();
            $contentType = $deleteRequest->getContentType();

            if (!empty($contentItem)) {
                $sales = null;
                $customersCount = null;

                if ($contentType == "course" or $contentType == "bundle") {
                    $sales = $contentItem->sales->whereNull('refund_at')->sum('total_amount');
                    $customersCount = $contentItem->sales->whereNull('refund_at')->count();
                } elseif ($contentType == "product") {
                    $sales = $contentItem->sales()->sum('total_amount');
                    $customersCount = $contentItem->salesCount();
                }

                $deleteRequest->update([
                    'content_title' => $contentItem->title,
                    'content_published_date' => $contentItem->created_at,
                    'customers_count' => $customersCount,
                    'sales' => $sales,
                ]);
            }

            return response()->json([
                'code' => 200,
                'title' => trans('public.request_success'),
                'msg' => trans('update.delete_request_saved_successfully_wait_for_admin_approval'),
            ]);
        }

        return response()->json([], 422);
    }

    private function checkCanAccess($itemId, $itemType)
    {
        $access = false;
        $user = auth()->user();

        if ($itemType == "App\Models\Webinar") {
            $webinar = Webinar::where('id', $itemId)
                ->where('creator_id', $user->id)
                ->first();

            $access = !empty($webinar);
        } elseif ($itemType == "App\Models\Bundle") {
            $bundle = Bundle::where('id', $itemId)
                ->where('creator_id', $user->id)
                ->first();

            $access = !empty($bundle);
        } elseif ($itemType == "App\Models\Product") {
            $product = Product::where('id', $itemId)
                ->where('creator_id', $user->id)
                ->first();

            $access = !empty($product);
        } elseif ($itemType == "App\Models\Blog") {
            $post = Blog::where('id', $itemId)
                ->where('author_id', $user->id)
                ->first();

            $access = !empty($post);
        }

        return $access;
    }

}
