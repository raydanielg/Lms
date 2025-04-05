<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\NotificationTemplate;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AbandonedCartController extends Controller
{

    public function settings(Request $request)
    {
        $this->authorize('admin_abandoned_cart_settings');

        removeContentLocale();

        $setting = Setting::where('page', 'general')
            ->where('name', Setting::$abandonedCartSettingsName)
            ->first();

        $notificationTemplates = NotificationTemplate::query()->get();

        $discounts = Discount::query()
            ->whereHas('creator', function ($query) {
                $query->whereHas('role', function ($query) {
                    $query->where('is_admin', true);
                });
            })
            ->where('status', 'active')
            ->where(function (Builder $query) {
                $query->whereNull('expired_at');
                $query->orWhere('expired_at', '<', time());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'pageTitle' => trans('update.settings'),
            'setting' => $setting,
            'selectedLocale' => mb_strtolower($request->get('locale', Setting::$defaultSettingsLocale)),
            'notificationTemplates' => $notificationTemplates,
            'discounts' => $discounts,
        ];

        return view('admin.abandoned_cart.settings.index', $data);
    }
}
