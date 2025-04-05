<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\ProductFilter;
use App\Models\ProductFilterOption;
use App\Models\Translation\ProductFilterOptionTranslation;
use App\Models\Translation\ProductFilterTranslation;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function index()
    {
        removeContentLocale();

        $this->authorize('admin_store_filters_list');

        $filters = ProductFilter::with('category')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/main.filters_list_page_title'),
            'filters' => $filters
        ];

        return view('admin.store.filters.lists', $data);
    }

    public function create()
    {
        $this->authorize('admin_store_filters_create');

        $categories = ProductCategory::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $data = [
            'pageTitle' => trans('admin/main.filter_new_page_title'),
            'categories' => $categories
        ];

        return view('admin.store.filters.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_store_filters_create');

        $this->validate($request, [
            'title' => 'required|min:3|max:128',
            'category_id' => 'required|exists:product_categories,id',
        ]);

        $data = $request->all();

        $filter = ProductFilter::create([
            'category_id' => $data['category_id'],
        ]);

        ProductFilterTranslation::updateOrCreate([
            'product_filter_id' => $filter->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
        ]);


        $filterOptions = !empty($data['sub_filters']) ? $data['sub_filters'] : [];
        $this->setSubFilters($filter, $filterOptions, $data['locale']);

        removeContentLocale();

        return redirect(getAdminPanelUrl().'/store/filters');
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('admin_store_filters_edit');

        $filter = ProductFilter::findOrFail($id);
        $categories = ProductCategory::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $filterOptions = ProductFilterOption::where('filter_id', $filter->id)
            ->orderBy('order', 'asc')
            ->get();

        $locale = $request->get('locale', app()->getLocale());
        storeContentLocale($locale, $filter->getTable(), $filter->id);

        $data = [
            'pageTitle' => trans('admin/main.admin_filters_edit'),
            'filter' => $filter,
            'categories' => $categories,
            'filterOptions' => $filterOptions,
        ];

        return view('admin.store.filters.create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_store_filters_edit');

        $this->validate($request, [
            'title' => 'required|min:3|max:128',
            'category_id' => 'required|exists:product_categories,id',
        ]);

        $data = $request->all();

        $filter = ProductFilter::findOrFail($id);
        $filter->update([
            'category_id' => $data['category_id'],
        ]);

        ProductFilterTranslation::updateOrCreate([
            'product_filter_id' => $filter->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
        ]);

        $filterOptions = !empty($data['sub_filters']) ? $data['sub_filters'] : [];
        $this->setSubFilters($filter, $filterOptions, $data['locale']);

        removeContentLocale();

        return back();
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_store_filters_delete');

        ProductFilter::find($id)->delete();

        removeContentLocale();

        return redirect(getAdminPanelUrl().'/store/filters');
    }

    public function setSubFilters(ProductFilter $filter, $filterOptions, $locale)
    {

        $allFilterOptionsIds = $filter->options->pluck('id')->toArray();

        if (!empty($filterOptions) and count($filterOptions)) {
            $order = 1;

            foreach ($filterOptions as $key => $filterOption) {
                if (!empty($filterOption['title'])) {
                    $oldFilterOption = ProductFilterOption::where('filter_id', $filter->id)
                        ->where('id', $key)
                        ->first();

                    if (!empty($oldFilterOption)) {

                        $oldIdsSearch = array_search($key, $allFilterOptionsIds);

                        if ($oldIdsSearch !== -1) {
                            unset($allFilterOptionsIds[$oldIdsSearch]);
                        }

                        $oldFilterOption->update([
                            'order' => $order,
                        ]);

                        ProductFilterOptionTranslation::updateOrCreate([
                            'product_filter_option_id' => $oldFilterOption->id,
                            'locale' => mb_strtolower($locale),
                        ], [
                            'title' => $filterOption['title'],
                        ]);
                    } else {
                        $option = ProductFilterOption::create([
                            'title' => $filterOption['title'],
                            'filter_id' => $filter->id,
                            'order' => $order,
                        ]);

                        ProductFilterOptionTranslation::updateOrCreate([
                            'product_filter_option_id' => $option->id,
                            'locale' => mb_strtolower($locale),
                        ], [
                            'title' => $filterOption['title'],
                        ]);
                    }

                    $order += 1;
                }
            }
        }

        if (!empty($allFilterOptionsIds) and count($allFilterOptionsIds)) {
            ProductFilterOption::whereIn('id', $allFilterOptionsIds)->delete();
        }
    }

    public function getByCategoryId($categoryId)
    {
        $filters = ProductFilter::where('category_id', $categoryId)
            ->with([
                'options' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
            ])
            ->get();

        return response()->json([
            'filters' => $filters,
        ], 200);
    }
}
