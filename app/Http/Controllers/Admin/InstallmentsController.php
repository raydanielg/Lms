<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\traits\InstallmentOrdersTrait;
use App\Http\Controllers\Admin\traits\InstallmentOverdueTrait;
use App\Http\Controllers\Admin\traits\InstallmentPurchasesTrait;
use App\Http\Controllers\Admin\traits\InstallmentSettingsTrait;
use App\Http\Controllers\Admin\traits\InstallmentVerificationRequestsTrait;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Group;
use App\Models\Installment;
use App\Models\InstallmentOrder;
use App\Models\InstallmentSpecificationItem;
use App\Models\InstallmentStep;
use App\Models\InstallmentUserGroup;
use App\Models\RegistrationPackage;
use App\Models\Subscribe;
use App\Models\Translation\InstallmentStepTranslation;
use App\Models\Translation\InstallmentTranslation;
use Illuminate\Http\Request;

class InstallmentsController extends Controller
{
    use InstallmentSettingsTrait;
    use InstallmentPurchasesTrait;
    use InstallmentOverdueTrait;
    use InstallmentVerificationRequestsTrait;
    use InstallmentOrdersTrait;

    public function index(Request $request)
    {
        $this->authorize('admin_installments_list');

        $installments = Installment::query()
            ->orderBy('created_at', 'desc')
            ->withCount([
                'steps'
            ])
            ->paginate(10);

        foreach ($installments as $installment) {
            $installment->sales_count = InstallmentOrder::query()
                ->where('installment_id', $installment->id)
                ->whereIn('status', ['open', 'pending_verification'])
                ->count();
        }

        $data = [
            'pageTitle' => trans('update.installment_plans'),
            'installments' => $installments,
        ];

        return view('admin.financial.installments.lists.index', $data);
    }

    public function create()
    {
        $this->authorize('admin_installments_create');

        $userGroups = Group::query()->where('status', 'active')->get();

        $categories = Category::where('parent_id', null)
            ->with('subCategories')
            ->get();
        $subscriptionPackages = Subscribe::all();
        $registrationPackages = RegistrationPackage::all();

        $data = [
            'pageTitle' => trans('update.new_installment_plan'),
            'userGroups' => $userGroups,
            'categories' => $categories,
            'subscriptionPackages' => $subscriptionPackages,
            'registrationPackages' => $registrationPackages,
        ];

        return view('admin.financial.installments.create.index', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_installments_create');

        $this->validate($request, [
            'title' => 'required',
            'main_title' => 'required',
            'description' => 'required',
            'target_type' => 'required',
            'upfront' => 'nullable|numeric',
        ]);

        $data = $request->all();

        $startDate = !empty($data['start_date']) ? convertTimeToUTCzone($data['start_date'], getTimezone())->getTimestamp() : null;
        $endDate = !empty($data['end_date']) ? convertTimeToUTCzone($data['end_date'], getTimezone())->getTimestamp() : null;

        $installment = Installment::query()->create([
            'target_type' => $data['target_type'],
            'target' => $data['target'] ?? null,
            'capacity' => $data['capacity'] ?? null,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'verification' => (!empty($data['verification']) and $data['verification'] == 'on'),
            'request_uploads' => (!empty($data['request_uploads']) and $data['request_uploads'] == 'on'),
            'bypass_verification_for_verified_users' => (!empty($data['bypass_verification_for_verified_users']) and $data['bypass_verification_for_verified_users'] == 'on'),
            'upfront' => $data['upfront'] ?? null,
            'upfront_type' => !empty($data['upfront']) ? $data['upfront_type'] : null,
            'enable' => (!empty($data['enable']) and $data['enable'] == 'on'),
            'created_at' => time(),
        ]);

        if (!empty($installment)) {
            $this->storeExtraData($installment, $data);

            $toastData = [
                'title' => trans('public.request_success'),
                'msg' => trans('update.new_installments_were_successfully_created'),
                'status' => 'success'
            ];

            return redirect(getAdminPanelUrl("/financial/installments/{$installment->id}/edit"))->with(['toast' => $toastData]);
        }

        abort(500);
    }

    private function storeExtraData($installment, $data)
    {
        InstallmentTranslation::updateOrCreate([
            'installment_id' => $installment->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
            'main_title' => $data['main_title'],
            'description' => $data['description'],
            'banner' => $data['banner'] ?? null,
            'options' => !empty($data['installment_options']) ? implode(Installment::$optionsExplodeKey, array_filter($data['installment_options'])) : null,
            'verification_description' => $data['verification_description'] ?? null,
            'verification_banner' => $data['verification_banner'] ?? null,
            'verification_video' => $data['verification_video'] ?? null,
        ]);

        InstallmentSpecificationItem::query()->where('installment_id', $installment->id)->delete();

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
            if (!empty($data[$key]) and $this->checkStoreSpecificationItems($key, $installment->target, $installment->target_type)) {
                $insert = [];

                foreach ($data[$key] as $item) {
                    $insert[] = [
                        'installment_id' => $installment->id,
                        $column => $item,
                    ];
                }

                if (!empty($insert)) {
                    InstallmentSpecificationItem::query()->insert($insert);
                }
            }
        }

        /* Steps */
        $ignoreStepIds = [];
        if (!empty($data['steps'])) {

            $order = 0;

            foreach ($data['steps'] as $stepId => $stepData) {
                if (!empty($stepData) and $stepId != "record" and !empty($stepData['title']) and !empty($stepData['amount']) and $stepData['amount'] > 0) {
                    $step = InstallmentStep::query()->where('id', $stepId)
                        ->where('installment_id', $installment->id)
                        ->first();

                    if (!empty($step)) {
                        $step->update([
                            'deadline' => $stepData['deadline'] ?? null,
                            'amount' => $stepData['amount'] ?? null,
                            'amount_type' => $stepData['amount_type'] ?? null,
                            'order' => $order,
                        ]);
                    } else {
                        $step = InstallmentStep::query()->create([
                            'installment_id' => $installment->id,
                            'deadline' => $stepData['deadline'] ?? null,
                            'amount' => $stepData['amount'] ?? null,
                            'amount_type' => $stepData['amount_type'] ?? null,
                            'order' => $order,
                        ]);
                    }

                    if (!empty($step)) {
                        $ignoreStepIds[] = $step->id;

                        InstallmentStepTranslation::query()->updateOrCreate([
                            'installment_step_id' => $step->id,
                            'locale' => mb_strtolower($data['locale']),
                        ], [
                            'title' => $stepData['title'],
                        ]);

                        $order = $order + 1;
                    }
                }
            }
        }

        InstallmentStep::query()->whereNotIn('id', $ignoreStepIds)
            ->where('installment_id', $installment->id)
            ->delete();

        /* User Groups */
        InstallmentUserGroup::query()->where('installment_id', $installment->id)->delete();
        if (!empty($data['group_ids'])) {
            $insert = [];

            foreach ($data['group_ids'] as $groupId) {
                if (!empty($groupId)) {
                    $insert[] = [
                        'installment_id' => $installment->id,
                        'group_id' => $groupId,
                    ];
                }
            }

            if (!empty($insert)) {
                InstallmentUserGroup::query()->insert($insert);
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
        $this->authorize('admin_installments_edit');

        $installment = Installment::query()->findOrFail($id);

        $userGroups = Group::query()->where('status', 'active')->get();

        $categories = Category::where('parent_id', null)
            ->with('subCategories')
            ->get();
        $subscriptionPackages = Subscribe::all();
        $registrationPackages = RegistrationPackage::all();

        $defaultLocal = getDefaultLocale();
        $locale = $request->get('locale', mb_strtolower($defaultLocal));
        storeContentLocale($locale, $installment->getTable(), $installment->id);

        $data = [
            'pageTitle' => trans('update.edit_installment_plan'),
            'userGroups' => $userGroups,
            'categories' => $categories,
            'subscriptionPackages' => $subscriptionPackages,
            'registrationPackages' => $registrationPackages,
            'installment' => $installment,
            'selectedLocale' => mb_strtolower($locale)
        ];

        return view('admin.financial.installments.create.index', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_installments_edit');

        $this->validate($request, [
            'title' => 'required',
            'main_title' => 'required',
            'description' => 'required',
            'target_type' => 'required',
            'upfront' => 'nullable|numeric',
        ]);

        $installment = Installment::query()->findOrFail($id);
        $data = $request->all();

        $startDate = !empty($data['start_date']) ? convertTimeToUTCzone($data['start_date'], getTimezone())->getTimestamp() : null;
        $endDate = !empty($data['end_date']) ? convertTimeToUTCzone($data['end_date'], getTimezone())->getTimestamp() : null;

        $installment->update([
            'target_type' => $data['target_type'],
            'target' => $data['target'] ?? null,
            'capacity' => $data['capacity'] ?? null,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'verification' => (!empty($data['verification']) and $data['verification'] == 'on'),
            'request_uploads' => (!empty($data['request_uploads']) and $data['request_uploads'] == 'on'),
            'bypass_verification_for_verified_users' => (!empty($data['bypass_verification_for_verified_users']) and $data['bypass_verification_for_verified_users'] == 'on'),
            'upfront' => $data['upfront'] ?? null,
            'upfront_type' => !empty($data['upfront']) ? $data['upfront_type'] : null,
            'enable' => (!empty($data['enable']) and $data['enable'] == 'on'),
        ]);

        if (!empty($installment)) {
            $this->storeExtraData($installment, $data);

            $toastData = [
                'title' => trans('public.request_success'),
                'msg' => trans('update.installment_were_successfully_updated'),
                'status' => 'success'
            ];

            return redirect(getAdminPanelUrl("/financial/installments/{$installment->id}/edit"))->with(['toast' => $toastData]);
        }

        abort(500);
    }

    public function delete($id)
    {
        $this->authorize('admin_installments_delete');

        $installment = Installment::query()->findOrFail($id);

        $installment->delete();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.installment_were_successfully_deleted'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/financial/installments"))->with(['toast' => $toastData]);
    }

}
