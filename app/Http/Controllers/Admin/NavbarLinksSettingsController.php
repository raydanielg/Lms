<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Translation\SettingTranslation;
use Illuminate\Http\Request;

class NavbarLinksSettingsController extends Controller
{
    public function index(Request $request)
    {
        $name = 'navbar_links';
        $this->authorize('admin_additional_pages_' . $name);

        $items = [];

        $settings = Setting::where('name', $name)
            ->first();

        $locale = $request->get('locale', getDefaultLocale());
        storeContentLocale($locale, $settings->getTable(), $settings->id);

        if (!empty($settings) and !empty($settings->value)) {
            $items = json_decode($settings->value, true);
        }

        $data = [
            'pageTitle' => trans('admin/main.additional_pages_title'),
            'items' => $items,
            'selectedLocal' => $locale
        ];

        return view('admin.additional_pages.' . $name, $data);
    }


    public function store(Request $request)
    {
        $this->authorize('admin_additional_pages_navbar_links');
        $this->validate($request, [
            'value.*' => 'required',
        ]);

        $data = $request->all();
        $locale = $request->get('locale', getDefaultLocale());
        $navbar_link = $data['navbar_link'];
        $values = [];

        $settings = Setting::where('name', Setting::$navbarLinkName)->first();

        $key = ($navbar_link !== 'newLink') ? $navbar_link : random_str(6);

        if (!empty($settings) and !empty($settings->value)) {
            $values = json_decode($settings->value);
        }

        $change = false;
        if (!empty($values)) {
            foreach ($values as $k => $value) {
                if ($k == $key) {
                    $values->$key = $data['value'];
                    $change = true;
                }
            }
        }

        if (!$change) {
            $newValue[$key] = $data['value'];
            $values = array_merge((array)$values, $newValue);
        }

        $settings = Setting::updateOrCreate(
            ['name' => Setting::$navbarLinkName],
            [
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

        removeContentLocale();

        cache()->forget('settings.' . Setting::$navbarLinkName);

        return redirect(getAdminPanelUrl().'/additional_page/navbar_links');
    }

    public function edit(Request $request, $link_key)
    {
        $this->authorize('admin_additional_pages_navbar_links');

        $settings = Setting::where('name', Setting::$navbarLinkName)->first();

        $locale = $request->get('locale', getDefaultLocale());
        storeContentLocale($locale, $settings->getTable(), $settings->id);

        if (!empty($settings)) {
            $values = [];

            if (!empty($settings->value)) {
                $values = json_decode($settings->value);
            }

            $result = null;

            if (!empty($values)) {
                foreach ($values as $key => $value) {
                    if ($key == $link_key) {
                        $result = $value;
                    }
                }
            }

            $data = [
                'pageTitle' => trans('admin/pages/setting.settings_navbar_links'),
                'navbar_link' => $result,
                'navbarLinkKey' => $link_key,
                'selectedLocal' => $locale
            ];

            return view('admin.additional_pages.navbar_links', $data);
        }

        abort(404);
    }

    public function delete($link_key)
    {
        $this->authorize('admin_additional_pages_navbar_links');
        $settings = Setting::where('name', Setting::$navbarLinkName)->first();

        if (!empty($settings) and !empty($settings->translations)) {
            foreach ($settings->translations as $translation) {
                $values = json_decode($translation->value);

                foreach ($values as $key => $value) {
                    if ($key == $link_key) {
                        unset($values->$link_key);
                    }
                }

                $settings->update([
                    'updated_at' => time(),
                ]);

                $translation->update([
                    'value' => json_encode($values),
                ]);
            }


            cache()->forget('settings.' . Setting::$navbarLinkName);

            return redirect()->back();
        }

        abort(404);
    }
}
