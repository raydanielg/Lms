<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\Models\Setting;
use App\Models\Translation\SettingTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RewardController extends Controller
{
    public function index()
    {
        $this->authorize('admin_rewards_history');

        $rewards = RewardAccounting::selectRaw('*,
                SUM(CASE WHEN status = "addiction" THEN score ELSE 0 END) as total_points,
                sum(case when status = "deduction" then score else 0 end) as spent_points
                ')
            ->groupBy('user_id')
            ->with([
                'user'
            ])
            ->paginate(10);

        foreach ($rewards as $reward) {
            $reward->available_points = $reward->total_points - $reward->spent_points;
        }

        $data = [
            'pageTitle' => trans('update.rewards'),
            'rewards' => $rewards
        ];

        return view('admin.rewards.history', $data);
    }

    public function create()
    {
        $this->authorize('admin_rewards_items');

        $rewards = Reward::orderBy('created_at', 'desc')->get();

        $data = [
            'pageTitle' => trans('update.rewards'),
            'rewards' => $rewards
        ];

        return view('admin.rewards.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_rewards_items');

        $data = $request->all();

        $validator = Validator::make($data, [
            'type' => 'required',
            'score' => Rule::requiredIf($data['type'] != 'badge'),
            'condition' => Rule::requiredIf(in_array($data['type'], ['charge_wallet', 'account_charge', 'buy', 'buy_store_product'])),
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $status = (!empty($data['status']) and $data['status'] == 'on') ? 'active' : 'disabled';

        $record = [
            'score' => $data['score'] ?? null,
            'type' => $data['type'],
            'status' => $status,
            'condition' => $data['condition'] ?? null,
            'created_at' => time()
        ];

        $reward = null;
        if (!empty($data['reward_id'])) {
            $reward = Reward::find($data['reward_id']);
        }

        if (!empty($reward)) {
            $reward->update($record);
        } else {
            Reward::create($record);
        }

        return response()->json([]);
    }

    public function edit($id)
    {
        $this->authorize('admin_rewards_items');

        $reward = Reward::find($id);

        return response()->json([
            'reward' => $reward
        ]);
    }

    public function delete($id)
    {
        $this->authorize('admin_rewards_item_delete');

        $reward = Reward::find($id);

        if ($reward) {
            $reward->delete();
        }

        return back();
    }

    public function settings()
    {
        $this->authorize('admin_rewards_settings');

        removeContentLocale();

        $setting = Setting::where('page', 'general')
            ->where('name', 'rewards_settings')
            ->first();

        if (!empty($setting)) {
            $setting->value = json_decode($setting->value, true);
        }

        $data = [
            'pageTitle' => trans('update.rewards_settings'),
            'itemValue' => !empty($setting) ? $setting->value : null,
        ];

        return view('admin.rewards.settings', $data);
    }

    public function storeSettings(Request $request)
    {
        $this->authorize('admin_rewards_settings');

        $page = 'general';
        $name = 'rewards_settings';

        $data = $request->all();
        $locale = $request->get('locale', Setting::$defaultSettingsLocale);
        $newValues = $data['value'];
        $values = [];


        $validator = Validator::make($data['value'], [
            'exchangeable_unit' => 'required_if:exchangeable,1',
        ]);

        $validator->validate();

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
