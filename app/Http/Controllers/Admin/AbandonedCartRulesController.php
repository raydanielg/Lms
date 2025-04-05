<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCartRule;
use App\Models\AbandonedCartRuleSpecificationItem;
use App\Models\AbandonedCartRuleUserGroup;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Group;
use App\Models\Translation\AbandonedCartRuleTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AbandonedCartRulesController extends Controller
{

    public function index()
    {
        $this->authorize("admin_abandoned_cart_rules");

        $time = time();
        $query = AbandonedCartRule::query();

        $totalRules = deepClone($query)->count();
        $activeRules = deepClone($query)->where('enable', true)
            ->where(function ($query) use ($time) {
                $query->whereNull('start_at');
                $query->orWhere('start_at', '<', $time);
            })
            ->where(function ($query) use ($time) {
                $query->whereNull('end_at');
                $query->orWhere('end_at', '>', $time);
            })->count();

        $totalActivities = 0;
        $totalSales = 0;

        $rules = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.rules'),
            'rules' => $rules,
            'totalRules' => $totalRules,
            'activeRules' => $activeRules,
            'totalActivities' => $totalActivities,
            'totalSales' => $totalSales,
        ];

        return view('admin.abandoned_cart.rules.lists.index', $data);
    }

    private function getDiscounts()
    {
        return Discount::query()
            ->whereHas('creator', function ($query) {
                $query->whereHas('role', function ($query) {
                    $query->where('is_admin', true);
                });
            })
            ->where('status', 'active')
            ->where(function (Builder $query) {
                $query->whereNull('expired_at');
                $query->orWhere('expired_at', '>', time());
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create()
    {
        $this->authorize("admin_abandoned_cart_rules");

        $discounts = $this->getDiscounts();
        $userGroups = Group::query()->where('status', 'active')->get();
        $categories = Category::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $data = [
            'pageTitle' => trans('update.new_rule'),
            'discounts' => $discounts,
            'userGroups' => $userGroups,
            'categories' => $categories,
        ];

        return view('admin.abandoned_cart.rules.create.index', $data);
    }

    public function store(Request $request)
    {
        $this->authorize("admin_abandoned_cart_rules");

        $this->validate($request, [
            'title' => 'required|string|max:255',
            'target_type' => 'required',
            'discount_id' => 'required_if:action,send_coupon',
            'action_cycle' => 'required|numeric'
        ]);

        $storeData = $this->handleStoreData($request);
        $rule = AbandonedCartRule::query()->create($storeData);

        // Store Extra Data
        $this->handleStoreExtraData($request, $rule);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.abandoned_cart_rule_created_successfully'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/abandoned-cart/rules/{$rule->id}/edit"))->with(['toast' => $toastData]);
    }


    public function edit(Request $request, $id)
    {
        $this->authorize("admin_abandoned_cart_rules");
        $rule = AbandonedCartRule::query()->findOrFail($id);

        $locale = $request->get('locale', app()->getLocale());
        storeContentLocale($locale, $rule->getTable(), $rule->id);

        $discounts = $this->getDiscounts();
        $userGroups = Group::query()->where('status', 'active')->get();
        $categories = Category::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $data = [
            'pageTitle' => trans('update.edit_rule') . ' ' . $rule->title,
            'rule' => $rule,
            'userGroups' => $userGroups,
            'categories' => $categories,
            'locale' => $locale,
            'discounts' => $discounts,
        ];

        return view('admin.abandoned_cart.rules.create.index', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize("admin_abandoned_cart_rules");
        $rule = AbandonedCartRule::query()->findOrFail($id);

        $this->validate($request, [
            'title' => 'required|string|max:255',
            'target_type' => 'required',
            'discount_id' => 'required_if:action,send_coupon',
            'action_cycle' => 'required|numeric'
        ]);

        $storeData = $this->handleStoreData($request, $rule);
        $rule->update($storeData);

        // Store Extra Data
        $this->handleStoreExtraData($request, $rule);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.abandoned_cart_rule_updated_successfully'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/abandoned-cart/rules/{$rule->id}/edit"))->with(['toast' => $toastData]);
    }

    public function delete($id)
    {
        $this->authorize("admin_abandoned_cart_rules");

        $rule = AbandonedCartRule::query()->findOrFail($id);
        $rule->delete();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.abandoned_cart_rule_deleted_successfully'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/abandoned-cart/rules"))->with(['toast' => $toastData]);
    }


    private function handleStoreData(Request $request, $rule = null)
    {
        $data = $request->all();
        $startDate = !empty($data['start_at']) ? convertTimeToUTCzone($data['start_at'], getTimezone())->getTimestamp() : null;
        $endDate = !empty($data['end_at']) ? convertTimeToUTCzone($data['end_at'], getTimezone())->getTimestamp() : null;
        $repeatAction = (!empty($data['repeat_action']) and $data['repeat_action'] == "1");

        return [
            'target_type' => $data['target_type'],
            'target' => $data['target'] ?? null,
            'action' => $data['action'],
            'discount_id' => !empty($data['discount_id']) ? $data['discount_id'] : null,
            'action_cycle' => $data['action_cycle'],
            'repeat_action' => $repeatAction,
            'repeat_action_count' => ($repeatAction and !empty($data['repeat_action_count'])) ? $data['repeat_action_count'] : null,
            'minimum_cart_amount' => !empty($data['minimum_cart_amount']) ? convertPriceToDefaultCurrency($data['minimum_cart_amount']) : null,
            'maximum_cart_amount' => !empty($data['maximum_cart_amount']) ? convertPriceToDefaultCurrency($data['maximum_cart_amount']) : null,
            'start_at' => $startDate,
            'end_at' => $endDate,
            'enable' => (!empty($data['enable']) and $data['enable'] == "1"),
            'created_at' => !empty($rule) ? $rule->created_at : time(),
        ];
    }

    private function handleStoreExtraData(Request $request, $rule)
    {
        $data = $request->all();

        AbandonedCartRuleTranslation::query()->updateOrCreate([
            'abandoned_cart_rule_id' => $rule->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
        ]);


        AbandonedCartRuleSpecificationItem::query()->where('abandoned_cart_rule_id', $rule->id)->delete();

        $specificationItems = [
            'category_ids' => 'category_id',
            'instructor_ids' => 'instructor_id',
            'seller_ids' => 'seller_id',
            'webinar_ids' => 'webinar_id',
            'product_ids' => 'product_id',
            'bundle_ids' => 'bundle_id',
        ];

        foreach ($specificationItems as $key => $column) {
            if (!empty($data[$key]) and $this->checkStoreSpecificationItems($key, $rule->target)) {
                $insert = [];

                foreach ($data[$key] as $item) {
                    $insert[] = [
                        'abandoned_cart_rule_id' => $rule->id,
                        $column => $item,
                    ];
                }

                if (!empty($insert)) {
                    AbandonedCartRuleSpecificationItem::query()->insert($insert);
                }
            }
        }



        /* Users And User Groups */
        AbandonedCartRuleUserGroup::query()->where('abandoned_cart_rule_id', $rule->id)->delete();

        if (!empty($data['group_ids'])) {
            $insert = [];

            foreach ($data['group_ids'] as $groupId) {
                if (!empty($groupId)) {
                    $insert[] = [
                        'abandoned_cart_rule_id' => $rule->id,
                        'group_id' => $groupId,
                    ];
                }
            }

            if (!empty($insert)) {
                AbandonedCartRuleUserGroup::query()->insert($insert);
            }
        }


        if (!empty($data['users_ids'])) {
            $insert = [];

            foreach ($data['users_ids'] as $userId) {
                if (!empty($userId)) {
                    $insert[] = [
                        'abandoned_cart_rule_id' => $rule->id,
                        'user_id' => $userId,
                    ];
                }
            }

            if (!empty($insert)) {
                AbandonedCartRuleUserGroup::query()->insert($insert);
            }
        }

    }


    private function checkStoreSpecificationItems($item, $target)
    {
        $store = false;

        $items = [
            'category_ids' => 'specific_categories',
            'instructor_ids' => 'specific_instructors',
            'seller_ids' => 'specific_sellers',
            'webinar_ids' => 'specific_courses',
            'product_ids' => 'specific_products',
            'bundle_ids' => 'specific_bundles',
        ];

        if ($items[$item] == $target) {
            $store = true;
        }

        return $store;
    }

}
