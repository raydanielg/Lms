<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Sale;
use App\Models\Translation\PromotionTranslation;
use Illuminate\Http\Request;

class PromotionsController extends Controller
{
    public function index()
    {
        $this->authorize('admin_promotion_list');

        removeContentLocale();

        $promotions = Promotion::orderBy('created_at', 'desc')->paginate(10);

        $data = [
            'pageTitle' => trans('admin/pages/financial.promotions'),
            'promotions' => $promotions
        ];

        return view('admin.financial.promotions.lists', $data);
    }

    public function create()
    {
        $this->authorize('admin_promotion_create');

        removeContentLocale();

        $data = [
            'pageTitle' => trans('admin/pages/financial.new_promotion')
        ];

        return view('admin.financial.promotions.new', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_promotion_create');

        $this->validate($request, [
            'title' => 'required|string',
            'days' => 'required|numeric',
            'price' => 'required|numeric',
            'icon' => 'required|string',
            'description' => 'required|string',
        ]);

        $data = $request->all();

        $promotion = Promotion::create([
            'days' => $data['days'],
            'price' => $data['price'],
            'icon' => $data['icon'],
            'is_popular' => $data['is_popular'],
            'created_at' => time(),
        ]);

        if (!empty($promotion)) {
            PromotionTranslation::updateOrCreate([
                'promotion_id' => $promotion->id,
                'locale' => mb_strtolower($data['locale']),
            ], [
                'title' => $data['title'],
                'description' => $data['description'],
            ]);
        }

        return redirect(getAdminPanelUrl().'/financial/promotions');
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('admin_promotion_edit');

        $promotion = Promotion::findOrFail($id);

        $locale = $request->get('locale', app()->getLocale());
        storeContentLocale($locale, $promotion->getTable(), $promotion->id);


        $data = [
            'pageTitle' => trans('admin/pages/financial.edit_promotion'),
            'promotion' => $promotion
        ];

        return view('admin.financial.promotions.new', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_promotion_create');

        $this->validate($request, [
            'title' => 'required|string',
            'days' => 'required|numeric',
            'price' => 'required|numeric',
            'icon' => 'required|string',
            'description' => 'required|string',
        ]);

        $promotion = Promotion::findOrFail($id);

        $data = $request->all();

        $promotion->update([
            'days' => $data['days'],
            'price' => $data['price'],
            'icon' => $data['icon'],
            'is_popular' => $data['is_popular'],
            'created_at' => time(),
        ]);

        PromotionTranslation::updateOrCreate([
            'promotion_id' => $promotion->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
            'description' => $data['description'],
        ]);

        removeContentLocale();

        return redirect(getAdminPanelUrl().'/financial/promotions');
    }

    public function delete($id)
    {
        $this->authorize('admin_promotion_delete');

        $promotion = Promotion::findOrFail($id);

        $promotion->delete();

        return redirect(getAdminPanelUrl().'/financial/promotions');
    }

    public function sales(Request $request)
    {
        $this->authorize('admin_promotion_list');

        $promotionSales = Sale::where('type', Sale::$promotion)
            ->whereNull('refund_at')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/pages/financial.promotion_sales'),
            'promotionSales' => $promotionSales
        ];

        return view('admin.financial.promotions.promotion_sales', $data);
    }
}
