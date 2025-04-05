<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashbackRule;
use App\Models\CashbackRuleSpecificationItem;
use App\Models\CashbackRuleUserGroup;
use App\Models\Category;
use App\Models\Group;
use App\Models\RegistrationPackage;
use App\Models\Subscribe;
use App\Models\Translation\CashbackRuleTranslation;
use Illuminate\Http\Request;

class CashbackRuleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_cashback_rules');

        $query = CashbackRule::query();

        $totalRules = deepClone($query)->count();
        $activeRules = deepClone($query)->where('enable', true)->count();
        $disabledRules = deepClone($query)->where('enable', false)->count();

        $rules = $this->handleFilters($request, $query)->paginate(10);

        $data = [
            'pageTitle' => trans('update.cashback_rules'),
            'rules' => $rules,
            'totalRules' => $totalRules,
            'activeRules' => $activeRules,
            'disabledRules' => $disabledRules,
        ];

        return view('admin.cashback.rules.lists.index', $data);
    }

    private function handleFilters(Request $request, $query)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $title = $request->get('title', null);
        $target_type = $request->get('target_type');
        $status = $request->get('status');
        $sort = $request->get('sort', null);

        if (!empty($from)) {
            $from = strtotime($from);

            $query->where('start_date', '>=', $from);
        }

        if (!empty($to)) {
            $to = strtotime($to);

            $query->where('end_date', '<', $to);
        }

        if (!empty($title)) {
            $query->whereTranslationLike('title', '%' . $title . '%');
        }


        if (!empty($target_type)) {
            $query->where('target_type', $target_type);
        }

        if (!empty($status)) {
            $enable = ($status == 'active');

            $query->where('enable', $enable);
        }

        if (!empty($sort)) {
            switch ($sort) {
                case 'amount_asc':
                    $query->orderBy('amount', 'asc');
                    break;
                case 'amount_desc':
                    $query->orderBy('amount', 'desc');
                    break;
                case 'paid_amount_asc':
                    // TODO::
                    break;
                case 'paid_amount_desc':
                    // TODO::
                    break;
                case 'date_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'date_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }


    public function create()
    {
        $this->authorize('admin_cashback_rules');

        $userGroups = Group::query()->where('status', 'active')->get();

        $categories = Category::where('parent_id', null)
            ->with('subCategories')
            ->get();
        $subscriptionPackages = Subscribe::all();
        $registrationPackages = RegistrationPackage::all();


        $data = [
            'pageTitle' => trans('update.new_rule'),
            'userGroups' => $userGroups,
            'categories' => $categories,
            'subscriptionPackages' => $subscriptionPackages,
            'registrationPackages' => $registrationPackages,
        ];

        return view('admin.cashback.rules.create.index', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_cashback_rules');

        $this->validate($request, [
            'title' => 'required',
            'target_type' => 'required',
            'amount' => 'required|numeric',
            'start_date' => 'required',
            'max_amount' => 'nullable|numeric',
            'min_amount' => 'nullable|numeric',
        ]);

        $data = $request->all();

        $startDate = !empty($data['start_date']) ? convertTimeToUTCzone($data['start_date'], getTimezone())->getTimestamp() : null;
        $endDate = !empty($data['end_date']) ? convertTimeToUTCzone($data['end_date'], getTimezone())->getTimestamp() : null;

        $rule = CashbackRule::query()->create([
            'target_type' => $data['target_type'],
            'target' => $data['target'] ?? null,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'amount' => $data['amount'],
            'amount_type' => $data['amount_type'],
            'apply_cashback_per_item' => ($data['amount_type'] == 'fixed_amount' and !empty($data['apply_cashback_per_item']) and $data['apply_cashback_per_item'] == 'on'),
            'max_amount' => ($data['amount_type'] == 'percent' and !empty($data['max_amount'])) ? $data['max_amount'] : null,
            'min_amount' => $data['min_amount'] ?? null,
            'enable' => (!empty($data['enable']) and $data['enable'] == 'on'),
            'created_at' => time(),
        ]);

        if (!empty($rule)) {
            $this->storeExtraData($rule, $data);

            $toastData = [
                'title' => trans('public.request_success'),
                'msg' => trans('update.new_cashback_rule_were_successfully_created'),
                'status' => 'success'
            ];

            return redirect(getAdminPanelUrl("/cashback/rules/{$rule->id}/edit"))->with(['toast' => $toastData]);
        }

        abort(500);
    }

    private function storeExtraData($rule, $data)
    {
        CashbackRuleTranslation::updateOrCreate([
            'cashback_rule_id' => $rule->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
        ]);

        CashbackRuleSpecificationItem::query()->where('cashback_rule_id', $rule->id)->delete();

        $specificationItems = [
            'category_ids' => 'category_id',
            'instructor_ids' => 'instructor_id',
            'seller_ids' => 'seller_id',
            'webinar_ids' => 'webinar_id',
            'product_ids' => 'product_id',
            'bundle_ids' => 'bundle_id',
            'subscribe_ids' => 'subscribe_id',
            'registration_package_ids' => 'registration_package_id',
        ];

        foreach ($specificationItems as $key => $column) {
            if (!empty($data[$key]) and $this->checkStoreSpecificationItems($key, $rule->target, $rule->target_type)) {
                $insert = [];

                foreach ($data[$key] as $item) {
                    $insert[] = [
                        'cashback_rule_id' => $rule->id,
                        $column => $item,
                    ];
                }

                if (!empty($insert)) {
                    CashbackRuleSpecificationItem::query()->insert($insert);
                }
            }
        }


        /* Users And User Groups */
        CashbackRuleUserGroup::query()->where('cashback_rule_id', $rule->id)->delete();

        if (!empty($data['group_ids'])) {
            $insert = [];

            foreach ($data['group_ids'] as $groupId) {
                if (!empty($groupId)) {
                    $insert[] = [
                        'cashback_rule_id' => $rule->id,
                        'group_id' => $groupId,
                    ];
                }
            }

            if (!empty($insert)) {
                CashbackRuleUserGroup::query()->insert($insert);
            }
        }


        if (!empty($data['users_ids'])) {
            $insert = [];

            foreach ($data['users_ids'] as $userId) {
                if (!empty($userId)) {
                    $insert[] = [
                        'cashback_rule_id' => $rule->id,
                        'user_id' => $userId,
                    ];
                }
            }

            if (!empty($insert)) {
                CashbackRuleUserGroup::query()->insert($insert);
            }
        }
    }

    private function checkStoreSpecificationItems($item, $target, $type)
    {
        $store = false;

        $items = [
            'category_ids' => 'specific_categories',
            'instructor_ids' => 'specific_instructors',
            'seller_ids' => 'specific_sellers',
            'webinar_ids' => 'specific_courses',
            'product_ids' => 'specific_products',
            'bundle_ids' => 'specific_bundles',
            'subscribe_ids' => 'specific_packages',
            'registration_package_ids' => 'specific_packages',
        ];

        if ($items[$item] == $target) {
            if ($item == 'subscribe_ids') {
                $store = ($type == 'subscription_packages');
            } else if ($item == 'registration_package_ids') {
                $store = ($type == 'registration_packages');
            } else {
                $store = true;
            }
        }

        return $store;
    }


    public function edit(Request $request, $id)
    {
        $this->authorize('admin_cashback_rules');

        $rule = CashbackRule::query()->findOrFail($id);

        $userGroups = Group::query()->where('status', 'active')->get();

        $categories = Category::where('parent_id', null)
            ->with('subCategories')
            ->get();
        $subscriptionPackages = Subscribe::all();
        $registrationPackages = RegistrationPackage::all();

        $defaultLocal = getDefaultLocale();
        $locale = $request->get('locale', mb_strtolower($defaultLocal));
        storeContentLocale($locale, $rule->getTable(), $rule->id);

        $data = [
            'pageTitle' => trans('update.edit_cashback_rule'),
            'userGroups' => $userGroups,
            'categories' => $categories,
            'subscriptionPackages' => $subscriptionPackages,
            'registrationPackages' => $registrationPackages,
            'rule' => $rule,
            'selectedLocale' => mb_strtolower($locale)
        ];

        return view('admin.cashback.rules.create.index', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_cashback_rules');

        $this->validate($request, [
            'title' => 'required',
            'target_type' => 'required',
            'amount' => 'required|numeric',
            'start_date' => 'required',
            'max_amount' => 'nullable|numeric',
            'min_amount' => 'nullable|numeric',
        ]);

        $rule = CashbackRule::query()->findOrFail($id);
        $data = $request->all();

        $startDate = !empty($data['start_date']) ? convertTimeToUTCzone($data['start_date'], getTimezone())->getTimestamp() : null;
        $endDate = !empty($data['end_date']) ? convertTimeToUTCzone($data['end_date'], getTimezone())->getTimestamp() : null;

        $rule->update([
            'target_type' => $data['target_type'],
            'target' => $data['target'] ?? null,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'amount' => $data['amount'],
            'amount_type' => $data['amount_type'],
            'apply_cashback_per_item' => ($data['amount_type'] == 'fixed_amount' and !empty($data['apply_cashback_per_item']) and $data['apply_cashback_per_item'] == 'on'),
            'max_amount' => ($data['amount_type'] == 'percent' and !empty($data['max_amount'])) ? $data['max_amount'] : null,
            'min_amount' => $data['min_amount'] ?? null,
            'enable' => (!empty($data['enable']) and $data['enable'] == 'on'),
        ]);

        if (!empty($rule)) {
            $this->storeExtraData($rule, $data);

            $toastData = [
                'title' => trans('public.request_success'),
                'msg' => trans('update.cashback_rule_were_successfully_updated'),
                'status' => 'success'
            ];

            return redirect(getAdminPanelUrl("/cashback/rules/{$rule->id}/edit"))->with(['toast' => $toastData]);
        }

        abort(500);
    }

    public function delete($id)
    {
        $this->authorize('admin_cashback_rules');

        $rule = CashbackRule::query()->findOrFail($id);

        $rule->delete();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.cashback_rule_were_successfully_deleted'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/cashback/rules"))->with(['toast' => $toastData]);
    }

    public function statusToggle($id)
    {
        $this->authorize('admin_cashback_rules');

        $rule = CashbackRule::query()->findOrFail($id);

        $rule->update([
            'enable' => !$rule->enable
        ]);


        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => '',
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/cashback/rules"))->with(['toast' => $toastData]);
    }
}
