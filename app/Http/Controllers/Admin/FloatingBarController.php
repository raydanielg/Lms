<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FloatingBar;
use App\Models\Translation\FloatingBarTranslation;
use Illuminate\Http\Request;

class FloatingBarController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('admin_floating_bar_create');

        $floatingBar = FloatingBar::query()->first();

        $defaultLocal = getDefaultLocale();
        $locale = $request->get('locale', mb_strtolower($defaultLocal));

        if (!empty($floatingBar)) {
            storeContentLocale($locale, $floatingBar->getTable(), $floatingBar->id);
        }

        $definedLanguage = [];
        if (!empty($floatingBar) and $floatingBar->translations) {
            $definedLanguage = $floatingBar->translations->pluck('locale')->toArray();
        }

        $data = [
            'pageTitle' => trans('update.top_bottom_bar'),
            'floatingBar' => $floatingBar,
            'definedLanguage' => $definedLanguage,
        ];

        return view('admin.floating_bar.index', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_floating_bar_create');

        $data = $request->all();

        $startDate = !empty($data['start_at']) ? convertTimeToUTCzone($data['start_at'], getTimezone())->getTimestamp() : null;
        $endDate = !empty($data['end_at']) ? convertTimeToUTCzone($data['end_at'], getTimezone())->getTimestamp() : null;

        $insert = [
            'start_at' => $startDate,
            'end_at' => $endDate,
            'title_color' => $data['title_color'] ?? null,
            'description_color' => $data['description_color'] ?? null,
            'icon' => $data['icon'] ?? null,
            'background_color' => $data['background_color'] ?? null,
            'background_image' => $data['background_image'] ?? null,
            'btn_url' => $data['btn_url'] ?? null,
            'btn_color' => $data['btn_color'] ?? null,
            'btn_text_color' => $data['btn_text_color'] ?? null,
            'bar_height' => $data['bar_height'] ?? null,
            'position' => $data['position'],
            'fixed' => (!empty($data['fixed'])),
            'enable' => (!empty($data['enable'])),
        ];

        $floatingBar = FloatingBar::query()->first();

        if (!empty($floatingBar)) {
            $floatingBar->update($insert);
        } else {
            $floatingBar = FloatingBar::query()->create($insert);
        }

        FloatingBarTranslation::updateOrCreate([
            'floating_bar_id' => $floatingBar->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'btn_text' => $data['btn_text'] ?? null,
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.floating_bar_successfully_updated'),
            'status' => 'success'
        ];

        if ($request->ajax()) {
            return response()->json([
                'code' => 200
            ]);
        }

        return redirect(getAdminPanelUrl("/floating_bars"))->with(['toast' => $toastData]);
    }
}
