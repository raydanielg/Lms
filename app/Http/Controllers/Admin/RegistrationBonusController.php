<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RegistrationBonusExport;
use App\Http\Controllers\Controller;
use App\Models\Accounting;
use App\Models\Affiliate;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\Translation\SettingTranslation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RegistrationBonusController extends Controller
{
    public function index(Request $request, $justReturnData = false)
    {
        $this->authorize('admin_registration_bonus_history');

        $registrationBonusSettings = getRegistrationBonusSettings();

        $query = User::query()
            ->where('status', User::$active)
            ->where('enable_registration_bonus', true)
            ->with([
                'role'
            ])
            ->withCount([
                'affiliates'
            ]);


        $unlockedBonusUserQuery = Accounting::query()
            ->where('is_registration_bonus', true)
            ->where('system', false);

        $achievedUsers = deepClone($query)->count();
        $unlockedBonusUsers = deepClone($unlockedBonusUserQuery)->count();
        $totalBonus = deepClone($query)->sum('registration_bonus_amount');
        $unlockedBonus = deepClone($unlockedBonusUserQuery)->sum('amount');


        $query = $this->handleFilters($request, $query);
        $users = $query->paginate(10);

        foreach ($users as $user) {

            $bonusAccounting = Accounting::query()
                ->where('user_id', $user->id)
                ->where('is_registration_bonus', true)
                ->where('system', false)
                ->first();

            //$user->bonus_wallet = $registrationBonusSettings['bonus_wallet'] ?? null;
            $user->bonus_status = !empty($bonusAccounting) ? trans('update.unlock') : trans('update.lock');

            if (!empty($registrationBonusSettings['unlock_registration_bonus_with_referral']) and !empty($registrationBonusSettings['number_of_referred_users'])) {

                $referredUsersId = Affiliate::query()->where('affiliate_user_id', $user->id)
                    ->pluck('referred_user_id')
                    ->toArray();

                $user->referred_users = count($referredUsersId);

                if (!empty($registrationBonusSettings['enable_referred_users_purchase'])) {
                    $sales = Sale::query()->select('buyer_id', DB::raw('sum(total_amount) as totalAmount'))
                        ->whereIn('buyer_id', $referredUsersId)
                        ->whereNull('refund_at')
                        ->groupBy('buyer_id')
                        ->orderBy('totalAmount', 'desc')
                        ->get();

                    $condition = !empty($registrationBonusSettings['purchase_amount_for_unlocking_bonus']) ? $registrationBonusSettings['purchase_amount_for_unlocking_bonus'] : 0;

                    $userPurchasedCount = 0;

                    foreach ($sales as $sale) {
                        if (empty($condition) and $sale->totalAmount > 0) {
                            $userPurchasedCount += 1;
                        } else if (!empty($condition) and $sale->totalAmount >= $condition) {
                            $userPurchasedCount += 1;
                        }
                    }

                    $user->referred_purchases = $userPurchasedCount;
                }
            }
        }

        if ($justReturnData) {
            return $users;
        }

        $roles = Role::query()->get();

        $data = [
            'pageTitle' => trans('update.bonus_history'),
            'achievedUsers' => $achievedUsers,
            'unlockedBonusUsers' => $unlockedBonusUsers,
            'totalBonus' => $totalBonus,
            'unlockedBonus' => $unlockedBonus,
            'users' => $users,
            'registrationBonusSettings' => $registrationBonusSettings,
            'roles' => $roles
        ];

        $user_ids = $request->get('user_ids', null);
        if (!empty($user_ids)) {
            $data['selectedUsers'] = User::query()->whereIn('id', $user_ids)->select('id', 'full_name')->get();
        }

        return view('admin.registration_bonus.history', $data);
    }

    private function handleFilters(Request $request, $query)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $title = $request->get('title', null);
        $user_ids = $request->get('user_ids', null);
        $role_id = $request->get('role_id', null);
        $bonus_wallet = $request->get('bonus_wallet');
        $bonus_status = $request->get('bonus_status');
        $sort = $request->get('sort', null);

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($title)) {
            $query->where('full_name', 'like', '%' . $title . '%');
        }

        if (!empty($user_ids)) {
            $query->whereIn('id', $user_ids);
        }

        if (!empty($role_id)) {
            $query->where('role_id', $role_id);
        }

        if (!empty($bonus_wallet)) {

        }

        if (!empty($bonus_status)) {
            if ($bonus_status == 'locked') {
                $query->whereDoesntHave('accounting', function ($query) {
                    $query->where('is_registration_bonus', true)
                        ->where('system', false);
                });
            } elseif ($bonus_status == 'unlocked') {
                $query->whereHas('accounting', function ($query) {
                    $query->where('is_registration_bonus', true)
                        ->where('system', false);
                });
            }
        }

        if (!empty($sort)) {
            switch ($sort) {
                case 'registration_date_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'registration_date_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'referred_users_asc':
                    $query->orderBy('affiliates_count', 'asc');
                    break;
                case 'referred_users_desc':
                    $query->orderBy('affiliates_count', 'desc');
                    break;
                case 'bonus_asc':
                    $query->orderBy('registration_bonus_amount', 'asc');
                    break;
                case 'bonus_desc':
                    $query->orderBy('registration_bonus_amount', 'desc');
                    break;
                case 'referred_purchases_asc':

                    break;
                case 'referred_purchases_desc':

                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('admin_registration_bonus_export_excel');

        $users = $this->index($request, true);

        $export = new RegistrationBonusExport($users);
        return Excel::download($export, 'registration_bonus.xlsx');
    }

    public function settings(Request $request)
    {
        $this->authorize('admin_registration_bonus_settings');

        removeContentLocale();

        $settings = Setting::where('page', 'general')
            ->whereIn('name', [Setting::$registrationBonusSettingsName, Setting::$registrationBonusTermsSettingsName])
            ->get();

        $data = [
            'pageTitle' => trans('update.registration_bonus_settings'),
            'settings' => $settings,
            'selectedLocale' => mb_strtolower($request->get('locale', Setting::$defaultSettingsLocale)),
        ];

        return view('admin.registration_bonus.settings.index', $data);
    }

    public function storeSettings(Request $request)
    {
        $this->authorize('admin_registration_bonus_settings');

        $page = 'general';
        $data = $request->all();
        $name = $data['name'];
        $locale = $data['locale'];
        $newValues = $data['value'];
        $values = [];

        $settings = Setting::where('name', $name)->first();

        if (!empty($settings) and !empty($settings->value)) {
            $values = json_decode($settings->value);
        }

        if (!empty($newValues) and !empty($values)) {
            foreach ($newValues as $newKey => $newValue) {
                foreach ($values as $key => $value) {
                    if ($key == $newKey) {
                        $values->$key = $newValue;
                        unset($newValues[$key]);
                    }
                }
            }
        }

        if (!empty($newValues)) {
            $values = array_merge((array)$values, $newValues);
        }

        $settings = Setting::updateOrCreate(
            ['name' => $name],
            [
                'page' => $page,
                'updated_at' => time(),
            ]
        );

        SettingTranslation::updateOrCreate(
            [
                'setting_id' => $settings->id,
                'locale' => mb_strtolower($locale)
            ],
            [
                'value' => json_encode($values),
            ]
        );

        cache()->forget('settings.' . $name);

        return back();
    }
}
