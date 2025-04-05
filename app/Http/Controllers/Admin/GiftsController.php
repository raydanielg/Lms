<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GiftHistoriesExport;
use App\Http\Controllers\Admin\traits\GiftsSettingsTrait;
use App\Http\Controllers\Controller;
use App\Models\Accounting;
use App\Models\Gift;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class GiftsController extends Controller
{
    use GiftsSettingsTrait;

    public function index(Request $request)
    {
        $this->authorize("admin_gift_history");

        $query = Gift::query()->where('status', '!=', 'pending')
            ->whereHas('sale'); // refund or not refund

        $topStats = $this->getTopStats($query);


        $gifts = $this->handleFilters($request, $query)->with([
            'sale',
            'user' => function ($query) {
                $query->select('id', 'full_name', 'role_name', 'role_id', 'mobile', 'email');
            },
            'receipt' => function ($query) {
                $query->select('id', 'full_name', 'role_name', 'role_id', 'mobile', 'email');
            },
            'webinar' => function ($query) {
                $query->select('id', 'creator_id', 'teacher_id', 'category_id', 'slug', 'status');
            },
            'bundle' => function ($query) {
                $query->select('id', 'creator_id', 'teacher_id', 'category_id', 'slug', 'status');
            },
            'product' => function ($query) {
                $query->select('id', 'creator_id', 'category_id', 'slug', 'status');
            },
        ])->paginate(10);

        foreach ($gifts as $gift) {
            $gift->receipt_status = !empty($gift->receipt);
        }

        $data = [
            'pageTitle' => trans('update.gifts_history'),
            'gifts' => $gifts,
        ];

        $data = array_merge($data, $topStats);

        $user_ids = $request->get('user_ids', null);
        if (!empty($user_ids)) {
            $data['selectedUsers'] = User::query()->whereIn('id', $user_ids)->select('id', 'full_name')->get();
        }

        return view("admin.gifts.history", $data);
    }

    public function exportExcel(Request $request)
    {
        $this->authorize("admin_gift_export");

        $query = Gift::query()->where('status', '!=', 'pending')
            ->whereHas('sale'); // refund or not refund

        $gifts = $this->handleFilters($request, $query)->with([
            'sale',
            'user' => function ($query) {
                $query->select('id', 'full_name', 'role_name', 'role_id', 'mobile', 'email');
            },
            'receipt' => function ($query) {
                $query->select('id', 'full_name', 'role_name', 'role_id', 'mobile', 'email');
            },
            'webinar' => function ($query) {
                $query->select('id', 'creator_id', 'teacher_id', 'category_id', 'slug', 'status');
            },
            'bundle' => function ($query) {
                $query->select('id', 'creator_id', 'teacher_id', 'category_id', 'slug', 'status');
            },
            'product' => function ($query) {
                $query->select('id', 'creator_id', 'category_id', 'slug', 'status');
            },
        ])->get();

        $export = new GiftHistoriesExport($gifts);
        return Excel::download($export, 'gift_history.xlsx');
    }

    private function handleFilters(Request $request, $query)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $search = $request->get('search', null);
        $sort = $request->get('sort', null);
        $receiptStatus = $request->get('receipt_status', null);
        $giftStatus = $request->get('gift_status', null);
        $userIds = $request->get('user_ids', null);

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($search)) {

            $query->where(function ($query) use ($search) {
                $query->whereHas('webinar', function ($query) use ($search) {
                    $query->whereTranslationLike('title', '%' . $search . '%');
                });

                $query->orWhereHas('bundle', function ($query) use ($search) {
                    $query->whereTranslationLike('title', '%' . $search . '%');
                });

                $query->orWhereHas('product', function ($query) use ($search) {
                    $query->whereTranslationLike('title', '%' . $search . '%');
                });

                $query->orWhereHas('user', function ($query) use ($search) {
                    $query->where('full_name', 'like', "%$search%");
                });

                $query->orWhereHas('receipt', function ($query) use ($search) {
                    $query->where('full_name', 'like', "%$search%");
                });

                $query->orWhere('name', 'like', "%$search%");
            });
        }

        if (!empty($userIds) and is_array($userIds)) {
            $query->where(function ($query) use ($userIds) {
                $query->whereHas('user', function ($query) use ($userIds) {
                    $query->whereIn('id', $userIds);
                });

                $query->orWhereHas('receipt', function ($query) use ($userIds) {
                    $query->whereIn('id', $userIds);
                });
            });
        }

        if (!empty($receiptStatus)) {
            if ($receiptStatus == "registered") {
                $query->whereHas('receipt');
            } elseif ($receiptStatus == "unregistered") {
                $query->whereDoesntHave('receipt');
            }
        }

        if (!empty($giftStatus)) {
            switch ($giftStatus) {
                case "pending":
                    $query->where(function ($query) {
                        $query->whereNotNull('date');
                        $query->where('date', '>', time());
                    });
                    break;
                case "sent":
                    $query->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereNull('date');
                            $query->orWhere('date', '<', time());
                        });
                        $query->where('status', 'active');
                    });
                    break;
                case "canceled":
                    $query->where('status', 'cancel');
                    break;
            }
        }

        if (!empty($sort)) {
            switch ($sort) {
                case "amount_asc":
                    $query->join('sales', 'sales.gift_id', '=', 'gifts.id')
                        ->select('gifts.*', 'sales.total_amount')
                        ->groupBy('sales.gift_id')
                        ->orderBy('sales.total_amount', 'asc');
                    break;
                case "amount_desc":
                    $query->join('sales', 'sales.gift_id', '=', 'gifts.id')
                        ->select('gifts.*', 'sales.total_amount')
                        ->groupBy('sales.gift_id')
                        ->orderBy('sales.total_amount', 'desc');
                    break;
                case "submit_date_asc":
                    $query->orderBy('created_at', 'asc');
                    break;
                case "submit_date_desc":
                    $query->orderBy('created_at', 'desc');
                    break;
                case "receive_date_asc":
                    $query->orderBy('date', 'asc');
                    break;
                case "receive_date_desc":
                    $query->orderBy('date', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    private function getTopStats($query)
    {
        $totalGifts = deepClone($query)->count();

        $totalSales = deepClone($query)->join('sales', 'sales.gift_id', 'gifts.id')
            ->select(DB::raw("sum(total_amount) as totalAmount"))
            ->first();


        $totalGiftAmount = (!empty($totalSales) and !empty($totalSales->totalAmount)) ? $totalSales->totalAmount : 0;

        $totalSenders = deepClone($query)
            ->select(DB::raw("count(user_id) as totalSenders"))
            ->groupBy('user_id')->get()->count();

        $totalReceipts = deepClone($query)
            ->select(DB::raw("count(email) as totalReceipts"))
            ->groupBy('email')->get()->count();

        return [
            'totalGifts' => $totalGifts,
            'totalGiftAmount' => $totalGiftAmount,
            'totalSenders' => $totalSenders,
            'totalReceipts' => $totalReceipts,
        ];
    }

    public function sendReminder(Request $request, $id)
    {
        $this->authorize("admin_gift_send_reminder");

        $gift = Gift::query()->findOrFail($id);

        $gift->sendReminderToRecipient(0);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.a_notification_has_been_sent_to_the_recipient_of_the_gift'),
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }

    public function cancel(Request $request, $id)
    {
        $this->authorize("admin_gift_cancel");

        $gift = Gift::query()->findOrFail($id);
        $sale = $gift->sale;

        if (!empty($sale)) {
            if (!empty($sale->total_amount)) {
                Accounting::refundAccounting($sale);
            }

            $sale->update(['refund_at' => time()]);
        }

        $gift->update([
            'status' => 'cancel'
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.the_gift_was_successfully_canceled'),
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }
}
