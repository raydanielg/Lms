<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\TrendCategory;
use Illuminate\Http\Request;

class TrendCategoriesController extends Controller
{
    public function index()
    {
        $this->authorize('admin_trending_categories');

        $trends = TrendCategory::with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('home.trending_categories'),
            'trends' => $trends,
        ];

        return view('admin.categories.trends_lists', $data);
    }

    public function create()
    {
        $this->authorize('admin_create_trending_categories');

        $categories = Category::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $data = [
            'pageTitle' => trans('admin/pages/categories.new_trend'),
            'categories' => $categories
        ];

        return view('admin.categories.create_trend', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_create_trending_categories');

        $this->validate($request, [
            'category_id' => 'required',
            'icon' => 'required',
            'color' => 'required',
        ]);

        $data = $request->all();

        TrendCategory::create([
            'category_id' => $data['category_id'],
            'icon' => $data['icon'],
            'color' => $data['color'],
            'created_at' => time(),
        ]);

        return redirect(getAdminPanelUrl().'/categories/trends');
    }

    public function edit($trend_id)
    {
        $this->authorize('admin_edit_trending_categories');

        $trend = TrendCategory::findOrFail($trend_id);

        $categories = Category::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $data = [
            'pageTitle' => trans('admin/pages/categories.new_trend'),
            'categories' => $categories,
            'trend' => $trend
        ];

        return view('admin.categories.create_trend', $data);
    }

    public function update(Request $request, $trend_id)
    {
        $this->authorize('admin_create_trending_categories');

        $this->validate($request, [
            'category_id' => 'required',
            'icon' => 'required',
            'color' => 'required',
        ]);

        $trend = TrendCategory::findOrFail($trend_id);
        $data = $request->all();

        $trend->update([
            'category_id' => $data['category_id'],
            'icon' => $data['icon'],
            'color' => $data['color'],
        ]);

        return redirect(getAdminPanelUrl().'/categories/trends');
    }

    public function destroy($trend_id)
    {
        $this->authorize('admin_delete_trending_categories');

        $trend = TrendCategory::findOrFail($trend_id);

        $trend->delete();

        return redirect(getAdminPanelUrl().'/categories/trends');
    }
}
