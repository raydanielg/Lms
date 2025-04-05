<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvertisingBanner;
use App\Models\Translation\AdvertisingBannerTranslation;
use Illuminate\Http\Request;

class AdvertisingBannersController extends Controller
{
    public function index()
    {
        $this->authorize('admin_advertising_banners');


        $banners = AdvertisingBanner::paginate(15);

        $data = [
            'pageTitle' => trans('admin/main.advertising_banners_list'),
            'banners' => $banners
        ];

        return view('admin.advertising.banner.lists', $data);
    }

    public function create()
    {
        $this->authorize('admin_advertising_banners_create');

        $data = [
            'pageTitle' => trans('admin/main.new_banner')
        ];

        return view('admin.advertising.banner.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_advertising_banners_create');

        $this->validate($request, [
            'title' => 'required',
            'position' => 'required',
            'image' => 'required',
            'size' => 'required',
            'link' => 'required',
        ]);

        $data = $request->all();

        $banner = AdvertisingBanner::create([
            'position' => $data['position'],
            'size' => $data['size'],
            'link' => $data['link'],
            'published' => $data['published'],
            'created_at' => time(),
        ]);

        AdvertisingBannerTranslation::updateOrCreate([
            'advertising_banner_id' => $banner->id,
            'locale' => mb_strtolower($data['locale'])
        ], [
            'title' => $data['title'],
            'image' => $data['image'],
        ]);

        return redirect(getAdminPanelUrl().'/advertising/banners');
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('admin_advertising_banners_edit');


        $banner = AdvertisingBanner::findOrFail($id);

        $locale = $request->get('locale', app()->getLocale());
        storeContentLocale($locale, $banner->getTable(), $banner->id);

        $data = [
            'pageTitle' => trans('admin/main.edit'),
            'banner' => $banner
        ];

        return view('admin.advertising.banner.create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_advertising_banners_edit');

        $this->validate($request, [
            'title' => 'required',
            'position' => 'required',
            'image' => 'required',
            'size' => 'required',
            'link' => 'required',
        ]);

        $data = $request->all();

        $banner = AdvertisingBanner::findOrFail($id);

        $banner->update([
            'position' => $data['position'],
            'size' => $data['size'],
            'link' => $data['link'],
            'published' => $data['published'],
        ]);

        AdvertisingBannerTranslation::updateOrCreate([
            'advertising_banner_id' => $banner->id,
            'locale' => mb_strtolower($data['locale'])
        ], [
            'title' => $data['title'],
            'image' => $data['image'],
        ]);

        removeContentLocale();

        return redirect(getAdminPanelUrl().'/advertising/banners');
    }

    public function delete($id)
    {
        $this->authorize('admin_advertising_banners_delete');

        $banner = AdvertisingBanner::findOrFail($id);

        $banner->delete();

        return redirect(getAdminPanelUrl().'/advertising/banners');
    }
}
