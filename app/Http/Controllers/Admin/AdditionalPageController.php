<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Models\Setting;
use App\Models\Translation\SettingTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdditionalPageController extends Controller
{
    public function index(Request $request, $name)
    {
        $this->authorize('admin_additional_pages_' . $name);

        $value = [];

        $settings = Setting::where('name', $name)
            ->first();

        $defaultLocale = Setting::$defaultSettingsLocale;

        if ($name == 'footer') {
            $defaultLocale = getDefaultLocale();
        }

        $locale = $request->get('locale', $defaultLocale);
        storeContentLocale($locale, $settings->getTable(), $settings->id);

        if (!empty($settings) and !empty($settings->value)) {
            $value = json_decode($settings->value, true);
        }

        $data = [
            'pageTitle' => trans('admin/main.additional_pages_title'),
            'value' => $value,
            'selectedLocal' => $locale
        ];

        return view('admin.additional_pages.' . $name, $data);
    }

    public function store(Request $request, $name)
    {
        if (!empty($request->get('name'))) {
            $name = $request->get('name');
        }

        $values = $request->get('value', null);

        if (!empty($values)) {
            $defaultLocale = Setting::$defaultSettingsLocale;

            if ($name == 'footer') {
                $defaultLocale = getDefaultLocale();
            }

            $locale = $request->get('locale', $defaultLocale);

            $values = array_filter($values, function ($val) {
                if (is_array($val)) {
                    return array_filter($val);
                } else {
                    return !empty($val);
                }
            });

            $values = json_encode($values);
            $values = str_replace('record', rand(1, 600), $values);

            $setting = Setting::updateOrCreate(
                ['name' => $name],
                [
                    'page' => $request->get('page', 'other'),
                    'updated_at' => time(),
                ]
            );

            SettingTranslation::updateOrCreate(
                [
                    'setting_id' => $setting->id,
                    'locale' => mb_strtolower($locale)
                ],
                [
                    'value' => $values,
                ]
            );

            cache()->forget('settings.' . $name);
        }

        removeContentLocale();

        return back();
    }

    public function storeFooter(Request $request)
    {
        $this->authorize('admin_additional_pages_footer');

        $newValues = $request->get('value', null);
        $locale = $request->get('locale', getDefaultLocale());
        $values = [];
        $settings = Setting::where('name', Setting::$footerName)->first();

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

        if (!empty($values)) {
            $values = json_encode($values);
            $values = str_replace('record', Str::random(8), $values);

            SettingTranslation::updateOrCreate(
                [
                    'setting_id' => $settings->id,
                    'locale' => mb_strtolower($locale)
                ],
                [
                    'value' => $values,
                ]
            );

            cache()->forget('settings.' . Setting::$footerName);

            removeContentLocale();

            return redirect(getAdminPanelUrl().'/additional_page/footer');
        }
    }
}
