<?php

namespace App\Http\Controllers\Panel\Store;

use App\Http\Controllers\Controller;
use App\Mixins\RegistrationPackage\UserPackage;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductMedia;
use App\Models\ProductOrder;
use App\Models\ProductSelectedFilterOption;
use App\Models\ProductSpecification;
use App\Models\ProductSpecificationCategory;
use App\Models\Translation\ProductTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $this->authorize("panel_products_lists");

        $user = auth()->user();

        if ((!$user->isTeacher() and !$user->isOrganization()) or !$user->checkCanAccessToStore()) {
            abort(403);
        }

        $query = Product::where('creator_id', $user->id);

        $physicalProducts = deepClone($query)->where('type', Product::$physical)->count();;
        $virtualProducts = deepClone($query)->where('type', Product::$virtual)->count();

        $totalPhysicalSales = deepClone($query)->where('products.type', Product::$physical)
            ->join('product_orders', 'products.id', 'product_orders.product_id')
            ->leftJoin('sales', function ($join) {
                $join->on('product_orders.id', '=', 'sales.product_order_id')
                    ->whereNull('sales.refund_at');
            })
            ->select(DB::raw('sum(sales.total_amount) as total_sales'))
            ->whereNotNull('product_orders.sale_id')
            ->whereNotIn('product_orders.status', [ProductOrder::$canceled, ProductOrder::$pending])
            ->first();

        $totalVirtualSales = deepClone($query)->where('products.type', Product::$virtual)
            ->join('product_orders', 'products.id', 'product_orders.product_id')
            ->leftJoin('sales', function ($join) {
                $join->on('product_orders.id', '=', 'sales.product_order_id')
                    ->whereNull('sales.refund_at');
            })
            ->select(DB::raw('sum(sales.total_amount) as total_sales'))
            ->whereNotNull('product_orders.sale_id')
            ->whereNotIn('product_orders.status', [ProductOrder::$canceled, ProductOrder::$pending])
            ->first();


        $products = deepClone($query)
            ->with([
                'productOrders'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.my_products'),
            'products' => $products,
            'physicalProducts' => $physicalProducts,
            'virtualProducts' => $virtualProducts,
            'physicalSales' => !empty($totalPhysicalSales) ? $totalPhysicalSales->total_sales : 0,
            'virtualSales' => !empty($totalVirtualSales) ? $totalVirtualSales->total_sales : 0,
        ];

        return view('web.default.panel.store.products.lists', $data);
    }

    public function create()
    {
        $this->authorize("panel_products_create");

        $user = auth()->user();

        if (!$user->checkCanAccessToStore()) {
            abort(403);
        }

        if (!$user->isTeacher() and !$user->isOrganization()) {
            abort(404);
        }

        $userPackage = new UserPackage();
        $userCoursesCountLimited = $userPackage->checkPackageLimit('product_count');

        if ($userCoursesCountLimited) {
            session()->put('registration_package_limited', $userCoursesCountLimited);

            return redirect()->back();
        }

        $data = [
            'pageTitle' => trans('update.new_product_page_title'),
            'currentStep' => 1,
        ];

        return view('web.default.panel.store.products.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize("panel_products_create");

        $user = auth()->user();

        if (!$user->checkCanAccessToStore()) {
            abort(403);
        }

        if (!$user->isTeacher() and !$user->isOrganization()) {
            abort(404);
        }

        $userPackage = new UserPackage();
        $userCoursesCountLimited = $userPackage->checkPackageLimit('product_count');

        if ($userCoursesCountLimited) {
            session()->put('registration_package_limited', $userCoursesCountLimited);

            return redirect()->back();
        }

        $rules = [
            'type' => 'required|in:' . implode(',', Product::$productTypes),
            'title' => 'required|max:255',
            'seo_description' => 'required|max:255',
            'summary' => 'required',
            'description' => 'required',
        ];

        $this->validate($request, $rules);

        $data = $request->all();

        $product = Product::create([
            'creator_id' => $user->id,
            'type' => $data['type'],
            'slug' => Product::makeSlug($data['title']),
            'category_id' => null,
            'price' => null,
            'unlimited_inventory' => false,
            'ordering' => (!empty($data['ordering']) and $data['ordering'] == 'on'),
            'inventory' => null,
            'inventory_warning' => null,
            'inventory_updated_at' => null,
            'delivery_fee' => null,
            'delivery_estimated_time' => null,
            'message_for_reviewer' => null,
            'status' => ((!empty($data['draft']) and $data['draft'] == 1) or (!empty($data['get_next']) and $data['get_next'] == 1)) ? Product::$draft : Product::$pending,
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
        }

        $notifyOptions = [
            '[u.name]' => $user->full_name,
            '[item_title]' => $product->title,
            '[content_type]' => trans('update.product'),
        ];
        sendNotification("new_item_created", $notifyOptions, 1);

        $url = '/panel/store/products';
        if ($data['get_next'] == 1) {
            $url = '/panel/store/products/' . $product->id . '/step/2';
        }

        return redirect($url);
    }

    public function edit(Request $request, $id, $step = 1)
    {
        $this->authorize("panel_products_create");

        $user = auth()->user();

        if (!$user->checkCanAccessToStore()) {
            abort(403);
        }

        if (!$user->isTeacher() and !$user->isOrganization()) {
            abort(404);
        }

        $locale = $request->get('locale', app()->getLocale());

        $query = Product::where('id', $id)
            ->where('creator_id', $user->id)
            ->with([
                'files' => function ($query) {
                    $query->orderBy('order', 'asc');
                }
            ]);

        if ($step == 4) {
            $query->with([
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
            ]);
        }

        $product = $query->first();

        if (empty($product)) {
            abort(404);
        }

        $data = [
            'pageTitle' => trans('update.edit_product') . ' | ' . $product->title,
            'product' => $product,
            'currentStep' => $step,
            'locale' => mb_strtolower($locale),
            'defaultLocale' => getDefaultLocale(),
        ];

        if ($step == 2) {
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

            $data['productCategoryFilters'] = $productCategoryFilters;
            $data['productCategories'] = $productCategories;
        } elseif ($step == 4) {
            $specificationIds = ProductSpecificationCategory::where('category_id', $product->category_id)
                ->pluck('specification_id')
                ->toArray();

            $data['productSpecifications'] = ProductSpecification::whereIn('id', $specificationIds)
                ->get();
        }

        return view('web.default.panel.store.products.create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize("panel_products_create");

        $user = auth()->user();

        if (!$user->checkCanAccessToStore()) {
            abort(403);
        }

        if (!$user->isTeacher() and !$user->isOrganization()) {
            abort(404);
        }

        $rules = [];
        $data = $request->all();
        $currentStep = $data['current_step'];
        $getStep = $data['get_step'];
        $getNextStep = (!empty($data['get_next']) and $data['get_next'] == 1);
        $isDraft = (!empty($data['draft']) and $data['draft'] == 1);

        $product = Product::where('id', $id)
            ->where('creator_id', $user->id)
            ->first();

        if (empty($product)) {
            abort(404);
        }

        if ($currentStep == 1) {

        }

        if ($currentStep == 2) {
            $rules = [
                'category_id' => 'required',
                'inventory' => 'required_without:unlimited_inventory'
            ];

            $data['unlimited_inventory'] = (!empty($data['unlimited_inventory']) and $data['unlimited_inventory'] == 'on');
        } elseif ($currentStep == 3) {
            $data['images'] = array_filter($data['images']);

            if (empty($data['images']) or !count($data['images'])) {
                $data['images'] = [];
            }

            $request->merge([ // for validation check
                'images' => $data['images']
            ]);

            $rules = [
                'thumbnail' => 'required',
                'images' => 'required|array|min:1|max:4',
            ];
        }

        $this->validate($request, $rules);

        $productRulesRequired = false;
        if (($currentStep == 5 and !$getNextStep and !$isDraft) or (!$getNextStep and !$isDraft)) {
            $productRulesRequired = empty($data['rules']);
        }

        $data['status'] = ($isDraft or $productRulesRequired) ? Product::$draft : Product::$pending;
        $data['updated_at'] = time();

        if ($currentStep == 1) {
            $data['ordering'] = (!empty($data['ordering']) and $data['ordering'] == 'on');

            ProductTranslation::updateOrCreate([
                'product_id' => $product->id,
                'locale' => mb_strtolower($data['locale']),
            ], [
                'title' => $data['title'],
                'seo_description' => $data['seo_description'],
                'summary' => $data['summary'],
                'description' => $data['description'],
            ]);
        } elseif ($currentStep == 2) {
            $data['price'] = !empty($data['price']) ? convertPriceToDefaultCurrency($data['price']) : null;
            $data['delivery_fee'] = !empty($data['delivery_fee']) ? convertPriceToDefaultCurrency($data['delivery_fee']) : null;

            $inventory = $data['inventory'];
            $productAvailability = $product->getAvailability();

            if ($inventory != $productAvailability) {
                $data['inventory_updated_at'] = time();
            }

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
        } elseif ($currentStep == 3) {
            $this->handleProductImages($product, $data);
        }

        unset($data['_token'],
            $data['current_step'],
            $data['draft'],
            $data['get_next'],
            $data['locale'],
            $data['get_step'],
            $data['ajax'],
            $data['title'],
            $data['description'],
            $data['seo_description'],
            $data['summary'],
            $data['thumbnail'],
            $data['images'],
            $data['video_demo'],
            $data['filters'],
        );

        if (isset($product->salesCountCache)) {
            unset($product->salesCountCache);
        }

        if (isset($product->availabilityCount)) {
            unset($product->availabilityCount);
        }

        $product->update($data);

        $url = '/panel/store/products';
        if ($getNextStep) {
            $nextStep = (!empty($getStep) and $getStep > 0) ? $getStep : $currentStep + 1;

            $url = '/panel/store/products/' . $product->id . '/step/' . (($nextStep <= 5) ? $nextStep : 5);
        }

        if ($productRulesRequired) {
            $url = '/panel/store/products/' . $product->id . '/step/5';

            return redirect($url)->withErrors(['rules' => trans('validation.required', ['attribute' => 'rules'])]);
        }

        if (!$getNextStep and !$isDraft and !$productRulesRequired) {
            $notifyOptions = [
                '[u.name]' => $user->full_name,
                '[item_title]' => $product->title,
                '[content_type]' => trans('update.product'),
            ];
            sendNotification("content_review_request", $notifyOptions, 1);
        }

        return redirect($url);
    }

    private function handleProductImages($product, $data)
    {
        $user = auth()->user();

        if (!empty($data['thumbnail'])) {
            ProductMedia::updateOrCreate([
                'creator_id' => $user->id,
                'product_id' => $product->id,
                'type' => ProductMedia::$thumbnail,
            ], [
                'path' => $data['thumbnail'],
                'created_at' => time(),
            ]);
        }

        if (!empty($data['images']) and count($data['images'])) {
            ProductMedia::where('creator_id', $user->id)
                ->where('product_id', $product->id)
                ->where('type', ProductMedia::$image)
                ->delete();

            foreach ($data['images'] as $image) {
                if (!empty($image)) {
                    ProductMedia::create([
                        'creator_id' => $user->id,
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
                'creator_id' => $user->id,
                'product_id' => $product->id,
                'type' => ProductMedia::$video,
            ], [
                'path' => $data['video_demo'],
                'created_at' => time(),
            ]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize("panel_products_delete");

        $user = auth()->user();

        if (!$user->checkCanAccessToStore()) {
            abort(403);
        }

        if (!$user->isTeacher() and !$user->isOrganization()) {
            abort(404);
        }

        if (!canDeleteContentDirectly()) {
            if ($request->ajax()) {
                return response()->json([], 422);
            } else {
                $toastData = [
                    'title' => trans('public.request_failed'),
                    'msg' => trans('update.it_is_not_possible_to_delete_the_content_directly'),
                    'status' => 'error'
                ];
                return redirect()->back()->with(['toast' => $toastData]);
            }
        }

        $product = Product::where('id', $id)
            ->where('creator_id', $user->id)
            ->first();

        if (!$product) {
            abort(404);
        }

        $product->delete();

        return response()->json([
            'code' => 200,
            'redirect_to' => $request->get('redirect_to')
        ], 200);
    }

    public function getContentItemByLocale(Request $request, $id)
    {
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

        $user = auth()->user();

        $product = Product::where('id', $id)
            ->where('creator_id', $user->id)
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

    public function getFilesModal($id)
    {
        $user = auth()->user();

        $product = Product::where('id', $id)->first();

        if (!empty($product) and !empty($product->files) and count($product->files) and $product->checkUserHasBought()) {
            $data = [
                'product' => $product
            ];

            $html = (string)view("web.default.products.includes.tabs.files", $data);

            return response()->json([
                'code' => 200,
                'html' => $html
            ]);
        }

        return response()->json([], 422);
    }

}
