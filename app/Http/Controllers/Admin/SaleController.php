<?php

namespace App\Http\Controllers\Admin;

use App\Exports\salesExport;
use App\Http\Controllers\Controller;
use App\Models\Accounting;
use App\Models\Order;
use App\Models\ReserveMeeting;
use App\Models\Sale;
use App\Models\SaleLog;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_sales_list');

        $query = Sale::whereNull('product_order_id');

        $totalSales = [
            'count' => deepClone($query)->count(),
            'amount' => deepClone($query)->sum('total_amount'),
        ];

        $classesSales = [
            'count' => deepClone($query)->whereNotNull('webinar_id')->count(),
            'amount' => deepClone($query)->whereNotNull('webinar_id')->sum('total_amount'),
        ];
        $appointmentSales = [
            'count' => deepClone($query)->whereNotNull('meeting_id')->count(),
            'amount' => deepClone($query)->whereNotNull('meeting_id')->sum('total_amount'),
        ];
        $failedSales = Order::where('status', Order::$fail)->count();

        $salesQuery = $this->getSalesFilters($query, $request);

        $sales = $salesQuery->orderBy('created_at', 'desc')
            ->with([
                'buyer',
                'webinar',
                'meeting',
                'subscribe',
                'promotion'
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
            'pageTitle' => trans('admin/pages/financial.sales_page_title'),
            'sales' => $sales,
            'totalSales' => $totalSales,
            'classesSales' => $classesSales,
            'appointmentSales' => $appointmentSales,
            'failedSales' => $failedSales,
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

        return view('admin.financial.sales.lists', $data);
    }

    private function makeTitle($sale)
    {
        if (!empty($sale->webinar_id) or !empty($sale->bundle_id)) {
            $item = !empty($sale->webinar_id) ? $sale->webinar : $sale->bundle;

            $sale->item_title = $item ? $item->title : trans('update.deleted_item');
            $sale->item_id = $item ? $item->id : '';
            $sale->item_seller = ($item and $item->creator) ? $item->creator->full_name : trans('update.deleted_item');
            $sale->seller_id = ($item and $item->creator) ? $item->creator->id : '';
            $sale->sale_type = ($item and $item->creator) ? $item->creator->id : '';
        } elseif (!empty($sale->meeting_id)) {
            $sale->item_title = trans('panel.meeting');
            $sale->item_id = $sale->meeting_id;
            $sale->item_seller = ($sale->meeting and $sale->meeting->creator) ? $sale->meeting->creator->full_name : trans('update.deleted_item');
            $sale->seller_id = ($sale->meeting and $sale->meeting->creator) ? $sale->meeting->creator->id : '';
        } elseif (!empty($sale->subscribe_id)) {
            $sale->item_title = !empty($sale->subscribe) ? $sale->subscribe->title : trans('update.deleted_subscribe');
            $sale->item_id = $sale->subscribe_id;
            $sale->item_seller = 'Admin';
            $sale->seller_id = '';
        } elseif (!empty($sale->promotion_id)) {
            $sale->item_title = !empty($sale->promotion) ? $sale->promotion->title : trans('update.deleted_promotion');
            $sale->item_id = $sale->promotion_id;
            $sale->item_seller = 'Admin';
            $sale->seller_id = '';
        } elseif (!empty($sale->registration_package_id)) {
            $sale->item_title = !empty($sale->registrationPackage) ? $sale->registrationPackage->title : 'Deleted registration Package';
            $sale->item_id = $sale->registration_package_id;
            $sale->item_seller = 'Admin';
            $sale->seller_id = '';
        } elseif (!empty($sale->gift_id) and !empty($sale->gift)) {
            $gift = $sale->gift;
            $item = !empty($gift->webinar_id) ? $gift->webinar : (!empty($gift->bundle_id) ? $gift->bundle : $gift->product);

            $sale->item_title = $gift->getItemTitle();
            $sale->item_id = $item->id;
            $sale->item_seller = $item->creator->full_name;
            $sale->seller_id = $item->creator_id;
        } elseif (!empty($sale->installment_payment_id) and !empty($sale->installmentOrderPayment)) {
            $installmentOrderPayment = $sale->installmentOrderPayment;
            $installmentOrder = $installmentOrderPayment->installmentOrder;
            $installmentItem = $installmentOrder->getItem();

            $sale->item_title = !empty($installmentItem) ? $installmentItem->title : '--';
            $sale->item_id = !empty($installmentItem) ? $installmentItem->id : '--';
            $sale->item_seller = !empty($installmentItem) ? $installmentItem->creator->full_name : '--';
            $sale->seller_id = !empty($installmentItem) ? $installmentItem->creator->id : '--';
        } else {
            $sale->item_title = '---';
            $sale->item_id = '---';
            $sale->item_seller = '---';
            $sale->seller_id = '';
        }

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

    public function refund($id)
    {
        $this->authorize('admin_sales_refund');

        $sale = Sale::findOrFail($id);

        if ($sale->type == Sale::$subscribe) {
            $salesWithSubscribe = Sale::whereNotNull('webinar_id')
                ->where('buyer_id', $sale->buyer_id)
                ->where('subscribe_id', $sale->subscribe_id)
                ->whereNull('refund_at')
                ->with('webinar', 'subscribe')
                ->get();

            foreach ($salesWithSubscribe as $saleWithSubscribe) {
                $saleWithSubscribe->update([
                    'refund_at' => time()
                ]);

                if (!empty($saleWithSubscribe->webinar) and !empty($saleWithSubscribe->subscribe)) {
                    Accounting::refundAccountingForSaleWithSubscribe($saleWithSubscribe->webinar, $saleWithSubscribe->subscribe);
                }
            }
        }

        if (!empty($sale->total_amount)) {
            Accounting::refundAccounting($sale);
        }

        if (!empty($sale->meeting_id) and $sale->type == Sale::$meeting) {
            $appointment = ReserveMeeting::where('meeting_id', $sale->meeting_id)
                ->where('sale_id', $sale->id)
                ->first();

            if (!empty($appointment)) {
                $appointment->update([
                    'status' => ReserveMeeting::$canceled
                ]);
            }
        }

        $sale->update(['refund_at' => time()]);

        return back();
    }

    public function invoice($id)
    {
        $this->authorize('admin_sales_invoice');

        $sale = Sale::where('id', $id)
            ->with([
                'order',

                'buyer' => function ($query) {
                    $query->select('id', 'full_name');
                },
                'webinar' => function ($query) {
                    $query->with([
                        'teacher' => function ($query) {
                            $query->select('id', 'full_name');
                        },
                        'creator' => function ($query) {
                            $query->select('id', 'full_name');
                        },
                        'webinarPartnerTeacher' => function ($query) {
                            $query->with([
                                'teacher' => function ($query) {
                                    $query->select('id', 'full_name');
                                },
                            ]);
                        }
                    ]);
                },
                'bundle'
            ])
            ->first();

        if (!empty($sale)) {
            $webinar = $sale->webinar;

            if (empty($webinar) and !empty($sale->bundle)) {
                $webinar = $sale->bundle;
            }

            if (!empty($webinar)) {
                $data = [
                    'pageTitle' => trans('webinars.invoice_page_title'),
                    'sale' => $sale,
                    'webinar' => $webinar
                ];

                return view('admin.financial.sales.invoice', $data);
            }
        }

        abort(404);
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('admin_sales_export');

        $query = Sale::query();

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
