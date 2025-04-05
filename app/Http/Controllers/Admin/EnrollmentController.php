<?php

namespace App\Http\Controllers\Admin;

use App\Exports\salesExport;
use App\Http\Controllers\Controller;
use App\Models\Accounting;
use App\Models\Bundle;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ReserveMeeting;
use App\Models\Sale;
use App\Models\SaleLog;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class EnrollmentController extends Controller
{
    public function history(Request $request)
    {
        $this->authorize('admin_enrollment_history');

        $query = Sale::whereNotNull('webinar_id');

        $salesQuery = $this->getSalesFilters($query, $request);

        $sales = $salesQuery->orderBy('created_at', 'desc')
            ->with([
                'buyer',
                'webinar',
            ])
            ->paginate(10);

        foreach ($sales as $sale) {
            $sale = $this->makeTitle($sale);

            if (empty($sale->saleLog)) {
                SaleLog::create([
                    'sale_id' => $sale->id,
                    'viewed_at' => time()
                ]);
            }
        }

        $data = [
            'pageTitle' => trans('public.history'),
            'sales' => $sales,
        ];

        $teacher_ids = $request->get('teacher_ids');
        $student_ids = $request->get('student_ids');
        $webinar_ids = $request->get('webinar_ids');

        if (!empty($teacher_ids)) {
            $data['teachers'] = User::select('id', 'full_name')
                ->whereIn('id', $teacher_ids)->get();
        }

        if (!empty($student_ids)) {
            $data['students'] = User::select('id', 'full_name')
                ->whereIn('id', $student_ids)->get();
        }

        if (!empty($webinar_ids)) {
            $data['webinars'] = Webinar::select('id')
                ->whereIn('id', $webinar_ids)->get();
        }

        return view('admin.enrollment.history', $data);
    }

    private function makeTitle($sale)
    {
        $item = $sale->webinar;

        $sale->item_title = $item ? $item->title : trans('update.deleted_item');
        $sale->item_id = $item ? $item->id : '';
        $sale->item_seller = ($item and $item->creator) ? $item->creator->full_name : trans('update.deleted_item');
        $sale->seller_id = ($item and $item->creator) ? $item->creator->id : '';
        $sale->sale_type = ($item and $item->creator) ? $item->creator->id : '';

        return $sale;
    }

    private function getSalesFilters($query, $request)
    {
        $item_title = $request->get('item_title');
        $from = $request->get('from');
        $to = $request->get('to');
        $status = $request->get('status');
        $webinar_ids = $request->get('webinar_ids', []);
        $teacher_ids = $request->get('teacher_ids', []);
        $student_ids = $request->get('student_ids', []);
        $userIds = array_merge($teacher_ids, $student_ids);

        if (!empty($item_title)) {
            $ids = Webinar::whereTranslationLike('title', "%$item_title%")->pluck('id')->toArray();
            $webinar_ids = array_merge($webinar_ids, $ids);
        }

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($status)) {
            if ($status == 'success') {
                $query->whereNull('refund_at');
            } elseif ($status == 'refund') {
                $query->whereNotNull('refund_at');
            } elseif ($status == 'blocked') {
                $query->where('access_to_purchased_item', false);
            }
        }

        if (!empty($webinar_ids) and count($webinar_ids)) {
            $query->whereIn('webinar_id', $webinar_ids);
        }

        if (!empty($userIds) and count($userIds)) {
            $query->where(function ($query) use ($userIds) {
                $query->whereIn('buyer_id', $userIds);
                $query->orWhereIn('seller_id', $userIds);
            });
        }

        return $query;
    }

    public function addStudentToClass()
    {
        $this->authorize('admin_enrollment_add_student_to_items');

        $data = [
            'pageTitle' => trans('update.add_student_to_a_class')
        ];

        return view('admin.enrollment.add_student_to_a_class', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_enrollment_add_student_to_items');

        $data = $request->all();

        $rules = [
            'user_id' => 'required|exists:users,id',
        ];

        if (!empty($data['webinar_id'])) {
            $rules['webinar_id'] = 'required|exists:webinars,id';
        } elseif (!empty($data['bundle_id'])) {
            $rules['bundle_id'] = 'required|exists:bundles,id';
        } elseif (!empty($data['product_id'])) {
            $rules['product_id'] = 'required|exists:products,id';
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response([
                    'code' => 422,
                    'errors' => $validator->errors(),
                ], 422);
            } else {
                return back()->withErrors($validator->errors()->getMessages());
            }
        }

        $user = User::find($data['user_id']);

        if (!empty($user)) {
            $sellerId = null;
            $itemType = null;
            $itemId = null;
            $itemColumnName = null;
            $checkUserHasBought = false;
            $isOwner = false;
            $product = null;

            if (!empty($data['webinar_id'])) {
                $course = Webinar::find($data['webinar_id']);

                if (!empty($course)) {
                    $sellerId = $course->creator_id;
                    $itemId = $course->id;
                    $itemType = Sale::$webinar;
                    $itemColumnName = 'webinar_id';
                    $isOwner = $course->isOwner($user->id);

                    $checkUserHasBought = $course->checkUserHasBought($user);
                }
            } elseif (!empty($data['bundle_id'])) {

                $bundle = Bundle::find($data['bundle_id']);

                if (!empty($bundle)) {
                    $sellerId = $bundle->creator_id;
                    $itemId = $bundle->id;
                    $itemType = Sale::$bundle;
                    $itemColumnName = 'bundle_id';
                    $isOwner = $bundle->isOwner($user->id);

                    $checkUserHasBought = $bundle->checkUserHasBought($user);
                }
            } elseif (!empty($data['product_id'])) {

                $product = Product::find($data['product_id']);

                if (!empty($product)) {
                    $sellerId = $product->creator_id;
                    $itemId = $product->id;
                    $itemType = Sale::$product;
                    $itemColumnName = 'product_order_id';

                    $isOwner = ($product->creator_id == $user->id);

                    $checkUserHasBought = $product->checkUserHasBought($user);
                }
            }

            $errors = [];

            if ($isOwner) {
                $errors = [
                    'user_id' => [trans('cart.cant_purchase_your_course')],
                    'webinar_id' => [trans('cart.cant_purchase_your_course')],
                    'bundle_id' => [trans('cart.cant_purchase_your_course')],
                    'product_id' => [trans('update.cant_purchase_your_product')],
                ];
            }

            if ((empty($errors) or !count($errors)) and $checkUserHasBought) {
                $errors = [
                    'user_id' => [trans('site.you_bought_webinar')],
                    'webinar_id' => [trans('site.you_bought_webinar')],
                    'bundle_id' => [trans('update.you_bought_bundle')],
                    'product_id' => [trans('update.you_bought_product')],
                ];
            }

            if (!empty($errors) and count($errors)) {
                if ($request->ajax()) {
                    return response([
                        'code' => 422,
                        'errors' => $errors,
                    ], 422);
                } else {
                    return back()->withErrors($errors);
                }
            }

            if (!empty($itemType) and !empty($itemId) and !empty($itemColumnName) and !empty($sellerId)) {

                $productOrder = null;
                if (!empty($product)) {
                    $productOrder = ProductOrder::create([
                        'product_id' => $product->id,
                        'seller_id' => $product->creator_id,
                        'buyer_id' => $user->id,
                        'specifications' => null,
                        'quantity' => 1,
                        'status' => 'pending',
                        'created_at' => time()
                    ]);

                    $itemId = $productOrder->id;
                    $itemType = Sale::$product;
                    $itemColumnName = 'product_order_id';
                }

                $sale = Sale::create([
                    'buyer_id' => $user->id,
                    'seller_id' => $sellerId,
                    $itemColumnName => $itemId,
                    'type' => $itemType,
                    'manual_added' => true,
                    'payment_method' => Sale::$credit,
                    'amount' => 0,
                    'total_amount' => 0,
                    'created_at' => time(),
                ]);

                if (!empty($product) and !empty($productOrder)) {
                    $productOrder->update([
                        'sale_id' => $sale->id,
                        'status' => $product->isVirtual() ? ProductOrder::$success : ProductOrder::$waitingDelivery,
                    ]);
                }

                if ($request->ajax()) {
                    return response()->json([
                        'code' => 200
                    ]);
                } else {
                    $toastData = [
                        'title' => trans('public.request_success'),
                        'msg' => trans('webinars.success_store'),
                        'status' => 'success'
                    ];
                    return redirect(getAdminPanelUrl().'/enrollments/history')->with(['toast' => $toastData]);
                }
            }
        }

        $errors = [
            'user_id' => [trans('update.something_went_wrong')],
            'webinar_id' => [trans('update.something_went_wrong')],
            'bundle_id' => [trans('update.something_went_wrong')],
            'product_id' => [trans('update.something_went_wrong')],
        ];

        if ($request->ajax()) {
            return response([
                'code' => 422,
                'errors' => $errors,
            ], 422);
        } else {
            return back()->withErrors($errors);
        }
    }

    public function blockAccess($saleId)
    {
        $this->authorize('admin_enrollment_block_access');

        $sale = Sale::where('id', $saleId)
            ->whereNull('refund_at')
            ->first();

        if (!empty($sale)) {
            if ($sale->manual_added) {
                $sale->delete();
            } else {
                $sale->update([
                    'access_to_purchased_item' => false
                ]);
            }

            $toastData = [
                'title' => trans('public.request_success'),
                'msg' => trans('update.delete-student-access_successfully'),
                'status' => 'success'
            ];
            return back()->with(['toast' => $toastData]);
        }

        abort(404);
    }

    public function enableAccess($saleId)
    {
        $this->authorize('admin_enrollment_enable_access');

        $sale = Sale::where('id', $saleId)
            ->whereNull('refund_at')
            ->first();

        if (!empty($sale)) {
            $sale->update([
                'access_to_purchased_item' => true
            ]);

            $toastData = [
                'title' => trans('public.request_success'),
                'msg' => trans('update.enable-student-access_successfully'),
                'status' => 'success'
            ];
            return back()->with(['toast' => $toastData]);
        }

        abort(404);
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('admin_sales_export');

        $query = Sale::whereNotNull('webinar_id');

        $salesQuery = $this->getSalesFilters($query, $request);

        $sales = $salesQuery->orderBy('created_at', 'desc')
            ->with([
                'buyer',
                'webinar',
                'meeting',
                'subscribe',
                'promotion'
            ])
            ->get();

        foreach ($sales as $sale) {
            $sale = $this->makeTitle($sale);
        }

        $export = new salesExport($sales);

        return Excel::download($export, 'sales.xlsx');
    }
}
