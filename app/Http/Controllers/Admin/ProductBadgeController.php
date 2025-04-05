<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductBadge;
use App\Models\Translation\ProductBadgeTranslation;
use Illuminate\Http\Request;

class ProductBadgeController extends Controller
{

    public function index()
    {
        $this->authorize("admin_product_badges_lists");

        $badges = ProductBadge::query()
            ->withCount(['contents'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.product_badges'),
            'badges' => $badges,
        ];

        return view('admin.product_badges.lists.index', $data);
    }


    public function create()
    {
        $this->authorize("admin_product_badges_create");

        $data = [
            'pageTitle' => trans('update.new_badge'),
        ];

        return view('admin.product_badges.create.index', $data);
    }

    public function store(Request $request)
    {
        $this->authorize("admin_product_badges_create");

        $this->validate($request, [
            'title' => 'required|string|max:255',
            'color' => 'required',
            'background' => 'required',
        ]);

        $storeData = $this->handleStoreData($request);
        $badge = ProductBadge::query()->create($storeData);

        // Store Extra Data
        $this->handleStoreExtraData($request, $badge);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.product_badge_created_successfully'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/product-badges/{$badge->id}/edit"))->with(['toast' => $toastData]);
    }


    public function edit(Request $request, $id)
    {
        $this->authorize("admin_product_badges_edit");
        $badge = ProductBadge::query()->findOrFail($id);

        $locale = $request->get('locale', app()->getLocale());
        storeContentLocale($locale, $badge->getTable(), $badge->id);

        $data = [
            'pageTitle' => trans('update.edit_badge') . ' ' . $badge->title,
            'badge' => $badge,
            'locale' => $locale,
        ];

        return view('admin.product_badges.create.index', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize("admin_product_badges_edit");
        $badge = ProductBadge::query()->findOrFail($id);

        $this->validate($request, [
            'title' => 'required|string|max:255',
            'color' => 'required',
            'background' => 'required',
        ]);

        $storeData = $this->handleStoreData($request, $badge);
        $badge->update($storeData);

        // Store Extra Data
        $this->handleStoreExtraData($request, $badge);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.product_badge_updated_successfully'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/product-badges/{$badge->id}/edit"))->with(['toast' => $toastData]);
    }

    public function delete($id)
    {
        $this->authorize("admin_product_badges_delete");

        $badge = ProductBadge::query()->findOrFail($id);
        $badge->delete();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.product_badge_deleted_successfully'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/product-badges"))->with(['toast' => $toastData]);
    }


    private function handleStoreData(Request $request, $badge = null)
    {
        $data = $request->all();
        $startDate = !empty($data['start_at']) ? convertTimeToUTCzone($data['start_at'], getTimezone())->getTimestamp() : null;
        $endDate = !empty($data['end_at']) ? convertTimeToUTCzone($data['end_at'], getTimezone())->getTimestamp() : null;


        return [
            'start_at' => $startDate,
            'end_at' => $endDate,
            'icon' => !empty($data['icon']) ? $data['icon'] : null,
            'color' => $data['color'],
            'background' => $data['background'],
            'enable' => (!empty($data['enable']) and $data['enable'] == "on"),
            'created_at' => !empty($badge) ? $badge->created_at : time(),
        ];
    }

    private function handleStoreExtraData(Request $request, $badge)
    {
        $data = $request->all();

        ProductBadgeTranslation::query()->updateOrCreate([
            'product_badge_id' => $badge->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
        ]);

    }

}
