<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Api\Bundle;
use App\Models\Discount;
use App\Models\DiscountBundle;
use App\Models\DiscountCategory;
use App\Models\DiscountCourse;
use App\Models\DiscountGroup;
use App\Models\DiscountUser;
use App\Models\Webinar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DiscountController extends Controller
{

    public function __construct()
    {
        if (empty(getFeaturesSettings("frontend_coupons_status"))) {
            abort(404);
        }
    }

    public function index(Request $request)
    {
        $this->authorize("panel_marketing_coupons");

        $user = auth()->user();

        $query = Discount::query()->where('creator_id', $user->id);

        $totalCoupons = deepClone($query)->count();
        $activeCoupons = deepClone($query)->where('status', 'active')->where('expired_at', '<', time())->count();
        $couponPurchases = 0;
        $purchaseAmount = 0;

        $query = $this->handleFilters($request, $query);
        $discounts = $query->paginate(10);

        $data = [
            'pageTitle' => trans('update.coupons'),
            'discounts' => $discounts,
            'totalCoupons' => $totalCoupons,
            'activeCoupons' => $activeCoupons,
            'couponPurchases' => $couponPurchases,
            'purchaseAmount' => $purchaseAmount,
        ];

        return view('web.default.panel.marketing.discounts.lists.index', $data);
    }

    private function handleFilters(Request $request, $query)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $source = $request->get('source');
        $status = $request->get('status');


        // $from and $to
        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($source) and $source != "all") {
            $query->where('source', $source);
        }

        if (!empty($status) and $status != "all") {
            if ($status == "active") {
                $query->where('status', 'active');
                $query->where('expired_at', '>', time());
            } elseif ($status == "expired") {
                $query->where('expired_at', '<', time());
            }
        }

        return $query;
    }

    public function create()
    {
        $this->authorize("panel_marketing_new_coupon");

        $data = $this->getCreateData();
        $data = array_merge($data, [
            'pageTitle' => trans('update.new_coupon'),
        ]);

        return view('web.default.panel.marketing.discounts.create.index', $data);
    }

    public function store(Request $request)
    {
        $this->authorize("panel_marketing_new_coupon");

        $user = auth()->user();

        $this->validate($request, [
            'title' => 'required',
            'discount_type' => 'required|in:' . implode(',', Discount::$discountTypes),
            'source' => 'required|in:' . implode(',', Discount::$panelDiscountSource),
            'code' => 'required|unique:discounts',
            'percent' => 'nullable',
            'amount' => 'nullable',
            'count' => 'nullable',
            'expired_at' => 'required',
        ]);

        $data = $request->all();
        $expiredAt = convertTimeToUTCzone($data['expired_at'], getTimezone());

        $discount = Discount::create([
            'creator_id' => $user->id,
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'discount_type' => $data['discount_type'],
            'source' => $data['source'],
            'code' => $data['code'],
            'percent' => (!empty($data['percent']) and $data['percent'] > 0) ? $data['percent'] : 0,
            'amount' => $data['amount'],
            'max_amount' => $data['max_amount'],
            'minimum_order' => $data['minimum_order'],
            'count' => (!empty($data['count']) and $data['count'] > 0) ? $data['count'] : 1,
            'user_type' => 'all_users',
            'product_type' => $data['product_type'] ?? null,
            'for_first_purchase' => false,
            'private' => (!empty($data['private']) and $data['private'] == "on"),
            'status' => 'active',
            'expired_at' => $expiredAt->getTimestamp(),
            'created_at' => time(),
        ]);

        $this->handleRelationItems($discount, $data);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.new_discount_coupon_has_been_created_successfully'),
            'status' => 'success'
        ];
        return redirect('/panel/marketing/discounts')->with(['toast' => $toastData]);
    }

    public function edit($id)
    {
        $this->authorize("panel_marketing_new_coupon");

        $user = auth()->user();
        $discount = Discount::where('id', $id)
            ->where('creator_id', $user->id)
            ->first();

        if (!empty($discount)) {

            $data = $this->getCreateData();
            $data = array_merge($data, [
                'pageTitle' => trans('update.edit_coupon'),
                'discount' => $discount,
            ]);

            return view('web.default.panel.marketing.discounts.create.index', $data);
        }

        abort(404);
    }

    public function update(Request $request, $id)
    {
        $this->authorize("panel_marketing_new_coupon");

        $user = auth()->user();
        $discount = Discount::where('id', $id)
            ->where('creator_id', $user->id)
            ->first();

        if (!empty($discount)) {

            $this->validate($request, [
                'title' => 'required',
                'discount_type' => 'required|in:' . implode(',', Discount::$discountTypes),
                'source' => 'required|in:' . implode(',', Discount::$panelDiscountSource),
                'code' => 'required|unique:discounts,code,' . $discount->id,
                'user_id' => 'nullable',
                'percent' => 'nullable',
                'amount' => 'nullable',
                'count' => 'nullable',
                'expired_at' => 'required',
            ]);

            $data = $request->all();
            $expiredAt = convertTimeToUTCzone($data['expired_at'], getTimezone());

            $discount->update([
                'title' => $data['title'],
                'subtitle' => $data['subtitle'] ?? null,
                'discount_type' => $data['discount_type'],
                'source' => $data['source'],
                'code' => $data['code'],
                'percent' => (!empty($data['percent']) and $data['percent'] > 0) ? $data['percent'] : 0,
                'amount' => $data['amount'],
                'max_amount' => $data['max_amount'],
                'minimum_order' => $data['minimum_order'],
                'count' => (!empty($data['count']) and $data['count'] > 0) ? $data['count'] : 1,
                'user_type' => 'all_users',
                'product_type' => $data['product_type'] ?? null,
                'for_first_purchase' => false,
                'private' => (!empty($data['private']) and $data['private'] == "on"),
                'status' => 'active',
                'expired_at' => $expiredAt->getTimestamp(),
            ]);

            DiscountCourse::where('discount_id', $discount->id)->delete();

            DiscountBundle::where('discount_id', $discount->id)->delete();

            $this->handleRelationItems($discount, $data);

            $toastData = [
                'title' => trans('public.request_success'),
                'msg' => trans('update.discount_coupon_has_been_updated_successfully'),
                'status' => 'success'
            ];
            return redirect("/panel/marketing/discounts/{$discount->id}/edit")->with(['toast' => $toastData]);
        }

        abort(404);
    }

    public function delete(Request $request, $id)
    {
        $this->authorize("panel_marketing_delete_coupon");

        $user = auth()->user();
        $discount = Discount::where('id', $id)
            ->where('creator_id', $user->id)
            ->first();

        if (!empty($discount)) {
            $discount->delete();

            $toastData = [
                'title' => trans('public.request_success'),
                'msg' => trans('update.discount_coupon_has_been_deleted_successfully'),
                'status' => 'success'
            ];
            return redirect("/panel/marketing/discounts")->with(['toast' => $toastData]);
        }

        abort(404);
    }


    private function getCreateData()
    {
        $user = auth()->user();

        $webinars = Webinar::query()->select('id', 'teacher_id', 'creator_id')
            ->where(function ($query) use ($user) {
                $query->where('creator_id', $user->id);
                $query->orWhere('teacher_id', $user->id);
            })->get();

        $bundles = Bundle::query()->select('id', 'teacher_id', 'creator_id')
            ->where(function ($query) use ($user) {
                $query->where('creator_id', $user->id);
                $query->orWhere('teacher_id', $user->id);
            })->get();

        return [
            'webinars' => $webinars,
            'bundles' => $bundles,
        ];
    }

    private function handleRelationItems($discount, $data)
    {
        $coursesIds = $data['webinar_ids'] ?? [];
        $bundlesIds = $data['bundle_ids'] ?? [];


        if (!empty($coursesIds) and count($coursesIds)) {
            foreach ($coursesIds as $coursesId) {
                DiscountCourse::create([
                    'discount_id' => $discount->id,
                    'course_id' => $coursesId,
                    'created_at' => time(),
                ]);
            }
        }

        if (!empty($bundlesIds) and count($bundlesIds)) {
            foreach ($bundlesIds as $bundlesId) {
                DiscountBundle::create([
                    'discount_id' => $discount->id,
                    'bundle_id' => $bundlesId,
                    'created_at' => time(),
                ]);
            }
        }
    }


}
