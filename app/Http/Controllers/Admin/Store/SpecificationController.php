<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\ProductSpecification;
use App\Models\ProductSpecificationCategory;
use App\Models\ProductSpecificationMultiValue;
use App\Models\Translation\ProductSpecificationMultiValueTranslation;
use App\Models\Translation\ProductSpecificationTranslation;
use Illuminate\Http\Request;

class SpecificationController extends Controller
{
    public function index()
    {
        $this->authorize('admin_store_specifications');

        $specifications = ProductSpecification::withCount('categories')->paginate(10);

        $data = [
            'pageTitle' => trans('update.specifications'),
            'specifications' => $specifications
        ];

        return view('admin.store.specifications.lists', $data);
    }

    public function create()
    {
        $this->authorize('admin_store_specifications_create');

        $categories = ProductCategory::whereNull('parent_id')
            ->with([
                'subCategories' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
            ])
            ->get();

        $data = [
            'pageTitle' => trans('update.add_new_specification'),
            'categories' => $categories
        ];

        return view('admin.store.specifications.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_store_specifications_create');

        $this->validate($request, [
            'title' => 'required|string|max:255',
            'category' => 'required',
            'input_type' => 'required|in:' . implode(',', ProductSpecification::$inputTypes)
        ]);

        $data = $request->all();

        if (empty($data['locale'])) {
            $data['locale'] = getDefaultLocale();
        }

        $specification = ProductSpecification::create([
            'input_type' => $data['input_type'],
        ]);

        ProductSpecificationTranslation::updateOrCreate([
            'product_specification_id' => $specification->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
        ]);

        $this->handleSpecificationCategories($specification, $data['category']);

        $hasMultiValues = (!empty($data['input_type']) and $data['input_type'] == 'multi_value');
        $this->setMultiValues($specification, $request->get('multi_values'), $hasMultiValues, $data['locale']);

        return redirect(getAdminPanelUrl().'/store/specifications');
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('admin_store_specifications_edit');

        $specification = ProductSpecification::findOrFail($id);

        $categories = ProductCategory::whereNull('parent_id')
            ->with([
                'subCategories' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
            ])
            ->get();

        $selectedCategories = $specification->categories->pluck('category_id')->toArray();


        $multiValues = ProductSpecificationMultiValue::where('specification_id', $specification->id)->get();

        $locale = $request->get('locale', app()->getLocale());
        storeContentLocale($locale, $specification->getTable(), $specification->id);

        $data = [
            'pageTitle' => trans('update.edit_specification'),
            'specification' => $specification,
            'categories' => $categories,
            'selectedCategories' => $selectedCategories,
            'multiValues' => $multiValues,
            'selectedLocale' => mb_strtolower($locale)
        ];

        return view('admin.store.specifications.create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_store_specifications_edit');

        $specification = ProductSpecification::findOrFail($id);

        $this->validate($request, [
            'title' => 'required|string|max:255',
            'category' => 'required',
            'input_type' => 'required|in:' . implode(',', ProductSpecification::$inputTypes)
        ]);

        $data = $request->all();

        if (empty($data['locale'])) {
            $data['locale'] = getDefaultLocale();
        }

        $specification->update([
            'input_type' => $data['input_type'],
        ]);

        ProductSpecificationTranslation::updateOrCreate([
            'product_specification_id' => $specification->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
        ]);

        $this->handleSpecificationCategories($specification, $data['category']);

        $hasMultiValues = (!empty($data['input_type']) and $data['input_type'] == 'multi_value');
        $this->setMultiValues($specification, $request->get('multi_values'), $hasMultiValues, $data['locale']);

        removeContentLocale();

        return redirect(getAdminPanelUrl().'/store/specifications');
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_store_specifications_delete');

        $specification = ProductSpecification::findOrFail($id);

        $specification->delete();

        return redirect(getAdminPanelUrl().'/store/specifications');
    }

    private function handleSpecificationCategories($specification, $categories)
    {
        ProductSpecificationCategory::where('specification_id', $specification->id)->delete();

        if ($categories and count($categories)) {
            foreach ($categories as $category) {
                ProductSpecificationCategory::create([
                    'specification_id' => $specification->id,
                    'category_id' => $category
                ]);
            }
        }
    }

    private function setMultiValues($specification, $multiValues, $hasMultiValues, $locale)
    {
        $oldIds = [];

        if ($hasMultiValues and !empty($multiValues) and count($multiValues)) {
            foreach ($multiValues as $key => $multiValue) {
                $check = ProductSpecificationMultiValue::where('id', $key)->first();

                if (is_numeric($key)) {
                    $oldIds[] = $key;
                }

                if (!empty($multiValue['title'])) {
                    if (!empty($check)) {

                        ProductSpecificationMultiValueTranslation::updateOrCreate([
                            'product_specification_multi_value_id' => $check->id,
                            'locale' => mb_strtolower($locale),
                        ], [
                            'title' => $multiValue['title'],
                        ]);
                    } else {
                        $new = ProductSpecificationMultiValue::create([
                            'specification_id' => $specification->id,
                        ]);

                        ProductSpecificationMultiValueTranslation::updateOrCreate([
                            'product_specification_multi_value_id' => $new->id,
                            'locale' => mb_strtolower($locale),
                        ], [
                            'title' => $multiValue['title'],
                        ]);

                        $oldIds[] = $new->id;
                    }
                }
            }
        }

        ProductSpecificationMultiValue::where('specification_id', $specification->id)
            ->whereNotIn('id', $oldIds)
            ->delete();

        return true;
    }
}
