<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentDeleteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContentDeleteRequestController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize("admin_content_delete_requests_lists");

        $query = ContentDeleteRequest::query()
            ->select('*', DB::raw("case
            when status = 'pending' then 'a'
            when status = 'approved' then 'b'
            when status = 'rejected' then 'c'
            end as status_order
        "));

        $requests = $this->handleFilters($request, $query)
            ->orderBy('status_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->with([
                'user' => function ($qu) {
                    $qu->select('id', 'full_name', 'role_name', 'email', 'mobile');
                }
            ])->paginate(10);


        $data = [
            'pageTitle' => trans('update.content_delete_requests'),
            'requests' => $requests
        ];

        return view('admin.content_delete_requests.index', $data);
    }

    private function handleFilters(Request $request, $query)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $search = $request->get('search');
        $content_type = $request->get('content_type');
        $status = $request->get('status');

        // $from and $to
        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->whereHas('webinar', function ($query) use ($search) {
                    $query->whereTranslationLike('title', "%$search%");
                });

                $query->orWhereHas('bundle', function ($query) use ($search) {
                    $query->whereTranslationLike('title', "%$search%");
                });

                $query->orWhereHas('product', function ($query) use ($search) {
                    $query->whereTranslationLike('title', "%$search%");
                });

                $query->orWhereHas('post', function ($query) use ($search) {
                    $query->whereTranslationLike('title', "%$search%");
                });
            });
        }

        if (!empty($content_type)) {
            switch ($content_type) {
                case 'course':
                    $query->where('targetable_type', 'App\Models\Webinar');
                    break;
                case 'bundle':
                    $query->where('targetable_type', 'App\Models\Bundle');
                    break;
                case 'product':
                    $query->where('targetable_type', 'App\Models\Product');
                    break;
                case 'post':
                    $query->where('targetable_type', 'App\Models\Blog');
                    break;
            }
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function approve($id)
    {
        $this->authorize("admin_content_delete_requests_actions");

        $deleteRequest = ContentDeleteRequest::query()->findOrFail($id);

        $deleteRequest->update([
            'status' => 'approved',
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

            /* Remove Content */
            $contentItem->delete();
        }

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.content_delete_request_approved_successful'),
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }

    public function reject($id)
    {
        $this->authorize("admin_content_delete_requests_actions");

        $deleteRequest = ContentDeleteRequest::query()->findOrFail($id);

        $deleteRequest->update([
            'status' => 'rejected',
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.content_delete_request_rejected_successful'),
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }
}
