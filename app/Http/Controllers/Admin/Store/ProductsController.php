<?php

namespace App\Http\Controllers\Admin\Store;

use App\Exports\StoreProductsExport;
use App\Http\Controllers\Admin\traits\ProductBadgeTrait;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductDiscount;
use App\Models\ProductMedia;
use App\Models\ProductOrder;
use App\Models\ProductSelectedFilterOption;
use App\Models\ProductSpecification;
use App\Models\ProductSpecificationCategory;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Translation\ProductTranslation;
use App\Models\Translation\SettingTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ProductsController extends Controller
{
    use ProductBadgeTrait;

    public function index(Request $request)
    {
        $this->authorize('admin_store_products');

        removeContentLocale();

        $query = Product::query();

        $topStatData = $this->getTopPageStats(deepClone($query));

        $query = $this->handleFilters($query, $request)
            ->with([
                'category',
                'creator' => function ($qu) {
                    $qu->select('id', 'full_name');
                },
            ]);

        $products = $query->paginate(10);

        $categories = ProductCategory::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $data = [
            'pageTitle' => trans('update.products'),
            'products' => $products,
            'categories' => $categories,
        ];

        $data = array_merge($data, $topStatData);

        return view('admin.store.products.lists', $data);
    }

    public function inHouseProducts(Request $request)
    {
        $this->authorize('admin_store_in_house_products');

        removeContentLocale();

        $adminRoleIds = Role::where('is_admin', true)->pluck('id')->toArray();

        $query = Product::query()
            ->whereHas('creator', function ($query) use ($adminRoleIds) {
                $query->whereIn('role_id', $adminRoleIds);
            });

        $topStatData = $this->getTopPageStats(deepClone($query));

        $query = $this->handleFilters($query, $request)
            ->with([
                'category',
                'creator' => function ($qu) {
                    $qu->select('id', 'full_name');
                },
            ]);

        $products = $query->paginate(10);

        $categories = ProductCategory::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $data = [
            'pageTitle' => trans('update.in-house-products'),
            'products' => $products,
            'categories' => $categories,
            'inHouseProducts' => true
        ];

        $data = array_merge($data, $topStatData);

        return view('admin.store.products.lists', $data);
    }

    private function getTopPageStats($query)
    {
        $totalPhysicalProducts = deepClone($query)->where('type', Product::$physical)->count();
        $totalPhysicalSales = deepClone($query)->where('type', Product::$physical)
            ->join('product_orders', 'products.id', 'product_orders.product_id')
            ->select(DB::raw('sum(quantity) as salesCount'))
            ->whereNotNull('product_orders.sale_id')
            ->whereNotIn('product_orders.status', [ProductOrder::$canceled, ProductOrder::$pending])
            ->first();

        $totalVirtualProducts = deepClone($query)->where('type', Product::$virtual)->count();
        $totalVirtualSales = deepClone($query)->where('type', Product::$virtual)
            ->join('product_orders', 'products.id', 'product_orders.product_id')
            ->select(DB::raw('sum(quantity) as salesCount'))
            ->whereNotNull('product_orders.sale_id')
            ->whereNotIn('product_orders.status', [ProductOrder::$canceled, ProductOrder::$pending])
            ->first();

        $totalSellers = deepClone($query)->groupBy('creator_id')->get()->count();

        $totalBuyers = deepClone($query)
            ->join('product_orders', 'products.id', 'product_orders.product_id')
            ->select(DB::raw('count(buyer_id) as buyerCount'))
            ->whereNotNull('product_orders.sale_id')
            ->whereNotIn('product_orders.status', [ProductOrder::$canceled, ProductOrder::$pending])
            ->groupBy('buyer_id')
            ->first();

        return [
            'totalPhysicalProducts' => $totalPhysicalProducts,
            'totalPhysicalSales' => !empty($totalPhysicalSales) ? $totalPhysicalSales->salesCount : 0,
            'totalVirtualProducts' => $totalVirtualProducts,
            'totalVirtualSales' => !empty($totalVirtualSales) ? $totalVirtualSales->salesCount : 0,
            'totalSellers' => $totalSellers,
            'totalBuyers' => !empty($totalBuyers) ? $totalBuyers->buyerCount : 0,
        ];
    }

    private function handleFilters($query, $request)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $title = $request->get('title', null);
        $creator_ids = $request->get('creator_ids', null);
        $category_id = $request->get('category_id', null);
        $status = $request->get('status', null);
        $sort = $request->get('sort', null);

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($title)) {
            $query->whereTranslationLike('title', '%' . $title . '%');
        }

        if (!empty($creator_ids) and count($creator_ids)) {
            $query->whereIn('creator_id', $creator_ids);
        }

        if (!empty($category_id)) {
            $query->where('category_id', $category_id);
        }

        if (!empty($status)) {
            $query->where('products.status', $status);
        }

        if (!empty($sort)) {
            switch ($sort) {
                case 'has_discount':
                    $now = time();

                    $productIdsHasDiscount = ProductDiscount::where('status', 'active')
                        ->where('from_date', '<', $now)
                        ->where('end_date', '>', $now)
                        ->pluck('product_id')
                        ->toArray();

                    $query->whereIn('id', $productIdsHasDiscount)
                        ->orderBy('created_at', 'desc');
                    break;
                case 'sales_asc':
                    $query->join('product_orders', 'products.id', '=', 'product_orders.product_id')
                        ->leftJoin('sales', function ($join) {
                            $join->on('product_orders.id', '=', 'sales.product_order_id')
                                ->whereNull('sales.refund_at');
                        })
                        ->select('products.*', 'sales.product_order_id', 'sales.refund_at', DB::raw('count(sales.product_order_id) as sales_count'))
                        ->whereNotNull('sales.product_order_id')
                        ->whereNull('sales.refund_at')
                        ->groupBy('products.id')
                        ->orderBy('sales_count', 'asc');
                    break;
                case 'sales_desc':
                    $query->join('product_orders', 'products.id', '=', 'product_orders.product_id')
                        ->leftJoin('sales', function ($join) {
                            $join->on('product_orders.id', '=', 'sales.product_order_id')
                                ->whereNull('sales.refund_at');
                        })
                        ->select('products.*', 'sales.product_order_id', 'sales.refund_at', DB::raw('count(sales.product_order_id) as sales_count'))
                        ->whereNotNull('sales.product_order_id')
                        ->whereNull('sales.refund_at')
                        ->groupBy('products.id')
                        ->orderBy('sales_count', 'desc');

                    break;

                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;

                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;

                case 'income_asc':
                    $query->join('product_orders', 'products.id', '=', 'product_orders.product_id')
                        ->leftJoin('sales', function ($join) {
                            $join->on('product_orders.id', '=', 'sales.product_order_id')
                                ->whereNull('sales.refund_at');
                        })
                        ->select('products.*', 'sales.product_order_id', 'sales.total_amount', 'sales.refund_at', DB::raw('(sum(sales.total_amount) - (sum(sales.tax) + sum(sales.commission))) as amounts'))
                        ->whereNotNull('sales.product_order_id')
                        ->whereNull('sales.refund_at')
                        ->groupBy('products.id')
                        ->orderBy('amounts', 'asc');
                    break;

                case 'income_desc':
                    $query->join('product_orders', 'products.id', '=', 'product_orders.product_id')
                        ->leftJoin('sales', function ($join) {
                            $join->on('product_orders.id', '=', 'sales.product_order_id')
                                ->whereNull('sales.refund_at');
                        })
                        ->select('products.*', 'sales.product_order_id', 'sales.total_amount', 'sales.refund_at', DB::raw('(sum(sales.total_amount) - (sum(sales.tax) + sum(sales.commission))) as amounts'))
                        ->whereNotNull('sales.product_order_id')
                        ->whereNull('sales.refund_at')
                        ->groupBy('products.id')
                        ->orderBy('amounts', 'desc');
                    break;

                case 'created_at_asc':
                    $query->orderBy('created_at', 'asc');
                    break;

                case 'created_at_desc':
                    $query->orderBy('created_at', 'desc');
                    break;

                case 'updated_at_asc':
                    $query->orderBy('updated_at', 'asc');
                    break;

                case 'updated_at_desc':
                    $query->orderBy('updated_at', 'desc');
                    break;

                case 'inventory_asc':
                    $query->join('product_orders', 'products.id', '=', 'product_orders.product_id')
                        ->leftJoin('sales', function ($join) {
                            $join->on('product_orders.id', '=', 'sales.product_order_id')
                                ->whereNull('sales.refund_at');
                        })
                        ->select('products.*', 'sales.product_order_id', 'sales.refund_at', DB::raw('(products.inventory - sum(product_orders.quantity)) as remaining_inventory'))
                        ->whereNotNull('products.inventory')
                        ->whereNotNull('product_orders.sale_id')
                        ->whereNull('sales.refund_at')
                        ->groupBy('products.id')
                        ->orderBy('remaining_inventory', 'asc');

                    break;

                case 'inventory_desc':
                    $query->join('product_orders', 'products.id', '=', 'product_orders.product_id')
                        ->leftJoin('sales', function ($join) {
                            $join->on('product_orders.id', '=', 'sales.product_order_id')
                                ->whereNull('sales.refund_at');
                        })
                        ->select('products.*', 'sales.product_order_id', 'sales.refund_at', DB::raw('(products.inventory - sum(product_orders.quantity)) as remaining_inventory'))
                        ->whereNotNull('products.inventory')
                        ->whereNotNull('product_orders.sale_id')
                        ->whereNull('sales.refund_at')
                        ->groupBy('products.id')
                        ->orderBy('remaining_inventory', 'desc');

                    break;

                case 'no_inventory':
                    $query->join('product_orders', 'products.id', '=', 'product_orders.product_id')
                        ->leftJoin('sales', function ($join) {
                            $join->on('product_orders.id', '=', 'sales.product_order_id')
                                ->whereNull('sales.refund_at');
                        })
                        ->select('products.*', 'sales.product_order_id', 'sales.refund_at', DB::raw('(products.inventory - sum(product_orders.quantity)) as remaining_inventory'))
                        ->whereNotNull('products.inventory')
                        ->whereNotNull('product_orders.sale_id')
                        ->whereNull('sales.refund_at')
                        ->groupBy('products.id')
                        ->havingRaw("remaining_inventory < 1");

                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }


        return $query;
    }

    public function create(Request $request)
    {
        $this->authorize('admin_store_new_product');

        $data = [
            'pageTitle' => trans('update.create_new_product'),
        ];

        return view('admin.store.products.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_store_new_product');

        $rules = [
            'creator_id' => 'required|exists:users,id',
            'type' => 'required|in:' . implode(',', Product::$productTypes),
            'title' => 'required|max:255',
            'slug' => 'max:255|unique:products,slug',
            'seo_description' => 'required|max:255',
            'summary' => 'required',
            'description' => 'required',
            'point' => 'nullable|integer',
            'tax' => 'nullable|integer',
            'commission' => 'nullable|integer',
        ];

        $this->validate($request, $rules);

        $data = $request->all();

        if (empty($data['slug'])) {
            $data['slug'] = Product::makeSlug($data['title']);
        }

        $commission = null;

        if (!empty($data['commission'])) {
            $commission = $data['commission'];

            if ($data['commission_type'] == "fixed_amount") {
                $commission = convertPriceToDefaultCurrency($commission);
            }
        }

        $product = Product::create([
            'creator_id' => $data['creator_id'],
            'type' => $data['type'],
            'slug' => $data['slug'],
            'category_id' => null,
            'price' => null,
            'unlimited_inventory' => false,
            'ordering' => (!empty($data['ordering']) and $data['ordering'] == 'on'),
            'inventory' => null,
            'inventory_warning' => null,
            'delivery_fee' => null,
            'delivery_estimated_time' => null,
            'message_for_reviewer' => null,
            'point' => $data['point'] ?? null,
            'tax' => $data['tax'] ?? null,
            'commission_type' => $data['commission_type'] ?? 'percent',
            'commission' => $commission,
            'status' => Product::$pending,
            'updated_at' => time(),
            'created_at' => time(),
        ]);

        if ($product) {
            ProductTranslation::updateOrCreate([
                'product_id' => $product->id,
                'locale' => mb_strtolower($data['locale']),
            ], [
                'title' => $data['title'],
                'seo_description' => $data['seo_description'],
                'summary' => $data['summary'],
                'description' => $data['description'],
            ]);

            $url = getAdminPanelUrl('/store/products/' . $product->id . '/edit?locale=' . $data['locale']);

            return redirect($url);
        }

        return back();
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('admin_store_edit_product');

        $product = Product::where('id', $id)
            ->with([
                'creator',
                'files' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
                'category' => function ($query) {
                    $query->with([
                        'filters' => function ($query) {
                            $query->with('options');
                        }
                    ]);
                },
                'selectedSpecifications' => function ($query) {
                    $query->orderBy('order', 'asc');
                    $query->with('specification');
                },
                'faqs' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
            ])
            ->first();

        if (empty($product)) {
            abort(404);
        }

        $locale = $request->get('locale', app()->getLocale());
        storeContentLocale($locale, $product->getTable(), $product->id);

        $productCategories = ProductCategory::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $productCategoryFilters = !empty($product->category) ? $product->category->filters : [];

        if (empty($product->category) and !empty($request->old('category_id'))) {
            $category = ProductCategory::where('id', $request->old('category_id'))->first();

            if (!empty($category)) {
                $productCategoryFilters = $category->filters;
            }
        }

        $specificationIds = ProductSpecificationCategory::where('category_id', $product->category_id)
            ->pluck('specification_id')
            ->toArray();

        $productSpecifications = ProductSpecification::whereIn('id', $specificationIds)
            ->get();

        $data = [
            'pageTitle' => trans('update.edit_product'),
            'product' => $product,
            'productCategoryFilters' => $productCategoryFilters,
            'productCategories' => $productCategories,
            'locale' => mb_strtolower($locale),
            'defaultLocale' => getDefaultLocale(),
            'productSpecifications' => $productSpecifications,
        ];

        return view('admin.store.products.create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_store_delete_product');

        $product = Product::findOrFail($id);

        $data = $request->all();

        $data['images'] = array_filter($data['images']);

        if (empty($data['images']) or !count($data['images'])) {
            $data['images'] = [];
        }

        $request->merge([
            'images' => $data['images']
        ]);

        $rules = [
            'creator_id' => 'required|exists:users,id',
            'type' => 'required|in:' . implode(',', Product::$productTypes),
            'title' => 'required|max:255',
            'slug' => 'max:255|unique:products,slug,' . $product->id,
            'seo_description' => 'required|max:255',
            'summary' => 'required',
            'description' => 'required',
            'point' => 'nullable|integer',
            'tax' => 'nullable|integer',
            'commission' => 'nullable|integer',
            'inventory' => 'required_without:unlimited_inventory',
            'thumbnail' => 'required',
            'images' => 'required|array|min:1|max:4',
            'category_id' => 'required',
        ];

        $this->validate($request, $rules);

        if (empty($data['slug'])) {
            $data['slug'] = Product::makeSlug($data['title']);
        }

        $data['unlimited_inventory'] = (!empty($data['unlimited_inventory']) and $data['unlimited_inventory'] == 'on');

        $inventory = $data['inventory'];
        $productAvailability = $product->getAvailability();

        if ($inventory != $productAvailability) {
            $data['inventory_updated_at'] = time();
        }

        if (isset($product->salesCountCache)) {
            unset($product->salesCountCache);
        }

        if (isset($product->availabilityCount)) {
            unset($product->availabilityCount);
        }

        $commission = null;

        if (!empty($data['commission'])) {
            $commission = $data['commission'];

            if ($data['commission_type'] == "fixed_amount") {
                $commission = convertPriceToDefaultCurrency($commission);
            }
        }

        $product->update([
            'creator_id' => $data['creator_id'],
            'type' => $data['type'],
            'slug' => $data['slug'],
            'category_id' => $data['category_id'],
            'price' => $data['price'],
            'unlimited_inventory' => $data['unlimited_inventory'],
            'ordering' => (!empty($data['ordering']) and $data['ordering'] == 'on'),
            'inventory' => $data['inventory'] ?? null,
            'inventory_warning' => $data['inventory_warning'] ?? null,
            'inventory_updated_at' => $data['inventory_updated_at'] ?? null,
            'delivery_fee' => $data['delivery_fee'] ?? null,
            'delivery_estimated_time' => $data['delivery_estimated_time'] ?? null,
            'message_for_reviewer' => $data['message_for_reviewer'] ?? null,
            'point' => $data['point'] ?? null,
            'tax' => $data['tax'] ?? null,
            'commission_type' => $data['commission_type'] ?? 'percent',
            'commission' => $commission,
            'status' => $data['status'],
            'updated_at' => time(),
        ]);

        ProductTranslation::updateOrCreate([
            'product_id' => $product->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
            'seo_description' => $data['seo_description'],
            'summary' => $data['summary'],
            'description' => $data['description'],
        ]);

        $this->handleProductImages($product, $data);

        ProductSelectedFilterOption::where('product_id', $product->id)->delete();

        $filters = $request->get('filters', null);
        if (!empty($filters) and is_array($filters)) {
            foreach ($filters as $filter) {
                ProductSelectedFilterOption::create([
                    'product_id' => $product->id,
                    'filter_option_id' => $filter
                ]);
            }
        }

        // Product Badge
        $this->handleProductBadges($product, $data);

        $url = getAdminPanelUrl('/store/products/' . $product->id . '/edit?locale=' . $data['locale']);

        return redirect($url);
    }

    private function handleProductImages($product, $data)
    {
        if (!empty($data['thumbnail'])) {
            ProductMedia::updateOrCreate([
                'creator_id' => $product->creator_id,
                'product_id' => $product->id,
                'type' => ProductMedia::$thumbnail,
            ], [
                'path' => $data['thumbnail'],
                'created_at' => time(),
            ]);
        }

        if (!empty($data['images']) and count($data['images'])) {
            ProductMedia::where('creator_id', $product->creator_id)
                ->where('product_id', $product->id)
                ->where('type', ProductMedia::$image)
                ->delete();

            foreach ($data['images'] as $image) {
                if (!empty($image)) {
                    ProductMedia::create([
                        'creator_id' => $product->creator_id,
                        'product_id' => $product->id,
                        'type' => ProductMedia::$image,
                        'path' => $image,
                        'created_at' => time(),
                    ]);
                }
            }
        }

        if (!empty($data['video_demo'])) {
            ProductMedia::updateOrCreate([
                'creator_id' => $product->creator_id,
                'product_id' => $product->id,
                'type' => ProductMedia::$video,
            ], [
                'path' => $data['video_demo'],
                'created_at' => time(),
            ]);
        }
    }


    public function destroy($id)
    {
        $this->authorize('admin_store_delete_product');

        $product = Product::where('id', $id)->first();

        if (!empty($product)) {
            $product->delete();
        }

        return back();
    }

    public function search(Request $request)
    {
        $term = $request->get('term');

        $option = $request->get('option', null);

        $query = Product::select('id')
            ->whereTranslationLike('title', "%$term%");

        if (!empty($option)) {

        }

        $products = $query->get();

        return response()->json($products, 200);
    }

    public function getContentItemByLocale(Request $request, $id)
    {
        $this->authorize('admin_store_new_product');

        $data = $request->all();

        $validator = Validator::make($data, [
            'item_id' => 'required',
            'locale' => 'required',
            'relation' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = Product::where('id', $id)
            ->first();

        if (!empty($product)) {

            $itemId = $data['item_id'];
            $locale = $data['locale'];
            $relation = $data['relation'];

            if (!empty($product->$relation)) {
                $item = $product->$relation->where('id', $itemId)->first();

                if (!empty($item)) {
                    foreach ($item->translatedAttributes as $attribute) {
                        try {
                            $item->$attribute = $item->translate(mb_strtolower($locale))->$attribute;
                        } catch (\Exception $e) {
                            $item->$attribute = null;
                        }
                    }

                    return response()->json([
                        'item' => $item
                    ], 200);
                }
            }
        }

        abort(403);
    }

    public function settings()
    {
        $this->authorize('admin_store_settings');

        removeContentLocale();

        $setting = Setting::where('page', 'general')
            ->where('name', Setting::$storeSettingsName)
            ->first();

        if (!empty($setting)) {
            $setting->value = json_decode($setting->value, true);
        }

        $data = [
            'pageTitle' => trans('update.store_settings'),
            'itemValue' => !empty($setting) ? $setting->value : null,
        ];

        return view('admin.store.settings', $data);
    }

    public function storeSettings(Request $request)
    {
        $this->authorize('admin_store_settings');

        $page = 'general';
        $name = Setting::$storeSettingsName;

        $data = $request->all();
        $locale = $request->get('locale', Setting::$defaultSettingsLocale);
        $newValues = $data['value'];
        $values = [];


        $validator = Validator::make($data['value'], [
            'exchangeable_unit' => 'required_if:exchangeable,1',
        ]);

        $validator->validate();

        $settings = Setting::where('name', $name)->first();

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

        $settings = Setting::updateOrCreate(
            ['name' => $name],
            [
                'page' => $page,
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

        cache()->forget('settings.' . $name);

        return back();
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('admin_store_export_products');

        $query = Product::query();

        if (!empty($request->get('in_house_products'))) {
            $adminRoleIds = Role::where('is_admin', true)->pluck('id')->toArray();

            $query->whereHas('creator', function ($query) use ($adminRoleIds) {
                $query->whereIn('role_id', $adminRoleIds);
            });

        }

        $products = $this->handleFilters($query, $request)
            ->with([
                'category',
                'creator' => function ($qu) {
                    $qu->select('id', 'full_name');
                },
            ])->get();

        $export = new StoreProductsExport($products);

        return Excel::download($export, 'storeProducts.xlsx');
    }


    public function approve(Request $request, $id)
    {
        $this->authorize('admin_store_edit_product');

        $bundle = Product::query()->findOrFail($id);

        $bundle->update([
            'status' => Product::$active
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.product_status_changes_to_approved'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/store/products"))->with(['toast' => $toastData]);
    }

    public function reject(Request $request, $id)
    {
        $this->authorize('admin_store_edit_product');

        $bundle = Product::query()->findOrFail($id);

        $bundle->update([
            'status' => Product::$inactive
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.product_status_changes_to_rejected'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/store/products"))->with(['toast' => $toastData]);
    }

    public function unpublish(Request $request, $id)
    {
        $this->authorize('admin_store_edit_product');

        $bundle = Product::query()->findOrFail($id);

        $bundle->update([
            'status' => Product::$pending
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.product_status_changes_to_unpublished'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/store/products"))->with(['toast' => $toastData]);
    }

}
