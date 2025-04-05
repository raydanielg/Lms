<?php

namespace App\Models\Api;

//use Illuminate\Database\Eloquent\Model;
use App\Models\Product as Model;
use App\Models\ProductOrder;
use App\Models\ProductSelectedFilterOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    public function getLabelAttribute()
    {
        if ($this->ordering and !empty($this->inventory) and $this->getAvailability() < 1) {
            return trans('update.out_of_stock');
        } elseif (!$this->ordering and $this->getActiveDiscount()) {
            return trans('update.ordering_off');
        } elseif ($this->getActiveDiscount()) {
            return trans('public.offer', ['off' => $this->getActiveDiscount()->percent]);
        } else {
            switch ($this->status) {
                case \App\Models\Product::$active:
                    return trans('public.active');
                case Product::$inactive:
                    return trans('public.rejected');
                case Product::$draft:
                    return trans('public.draft');
                case Product::$pending:
                    return trans('public.waiting');
                default:
                    return null;
            }
        }
    }

    public function getWaitingOrdersAttribute()
    {
        return $this->productOrders->whereIn('status', [ProductOrder::$waitingDelivery, ProductOrder::$shipped])->count();
    }

    public function canAddToCart($user)
    {

    }

    public function checkProductForSale($user)
    {
        if ($this->getAvailability() < 1) {


            return apiResponse2(0, 'not_availability', trans('update.product_not_availability'));
        }

        if ($this->creator_id == $user->id) {

            return apiResponse2(0, 'same_user', trans('update.cant_purchase_your_product'));
        }

        return 'ok';
    }

    public function scopeHandleFilters($query)
    {
        $isRewardProducts = false;
        $request = \request();
        $isFree = $request->get('free', null);
        $isFreeShipping = $request->get('free_shipping', null);
        $withDiscount = $request->get('discount', null);
        $sort = $request->get('sort', null);
        $type = $request->get('type', null);
        $options = $request->get('options', null);
        $categoryId = $request->get('cat', null);
        $filterOption = $request->get('filter_option', null);

        if (!empty($isFree) and $isFree == '1') {
            $query->where(function ($qu) {
                $qu->whereNull('price')
                    ->orWhere('price', '0');
            });
        }

        if (!empty($isFreeShipping) and $isFreeShipping == '1') {
            $query->where(function ($qu) {
                $qu->whereNull('delivery_fee')
                    ->orWhere('delivery_fee', '0');
            });
        }

        if (!empty($withDiscount) and $withDiscount == '1') {
            $query->whereHas('discounts', function ($query) {
                $query->where('status', 'active')
                    ->where('start_date', '<', time())
                    ->where('end_date', '>', time());
            });
        }

        if (!empty($type) and count($type)) {
            $query->whereIn('type', $type);
        }

        if (!empty($options) and count($options)) {
            if (in_array('only_available', $options)) {
                $query->where(function ($query) {
                    $query->where('unlimited_inventory', true)
                        ->orWhereHas('productOrders', function ($query) {
                            $query->havingRaw('products.inventory > sum(quantity)')
                                ->whereNotNull('sale_id')
                                ->whereNotIn('status', [ProductOrder::$canceled, ProductOrder::$pending])
                                ->groupBy('product_id');
                        });
                });
            }

            if (in_array('with_point', $options)) {
                $query->whereNotNull('point');
            }
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if (!empty($filterOption) and is_array($filterOption)) {
            $productIdsFilterOptions = ProductSelectedFilterOption::whereIn('filter_option_id', $filterOption)
                ->pluck('product_id')
                ->toArray();

            $productIdsFilterOptions = array_unique($productIdsFilterOptions);

            $query->whereIn('products.id', $productIdsFilterOptions);
        }

        if (!empty($sort)) {
            if ($sort == 'expensive') {
                if ($isRewardProducts) {
                    $query->orderBy('point', 'desc');
                } else {
                    $query->orderBy('price', 'desc');
                }
            }

            if ($sort == 'inexpensive') {
                if ($isRewardProducts) {
                    $query->orderBy('point', 'asc');
                } else {
                    $query->orderBy('price', 'asc');
                }
            }

            if ($sort == 'bestsellers') {
                $query->leftJoin('product_orders', function ($join) {
                    $join->on('products.id', '=', 'product_orders.product_id')
                        ->whereNotNull('product_orders.sale_id')
                        ->whereNotIn('product_orders.status', [ProductOrder::$canceled, ProductOrder::$pending]);
                })
                    ->select('products.*', DB::raw('sum(product_orders.quantity) as salesCounts'))
                    ->groupBy('product_orders.product_id')
                    ->orderBy('salesCounts', 'desc');
            }

            if ($sort == 'best_rates') {
                $query->leftJoin('product_reviews', function ($join) {
                    $join->on('products.id', '=', 'product_reviews.product_id');
                    $join->where('product_reviews.status', 'active');
                })
                    ->whereNotNull('rates')
                    ->select('products.*', DB::raw('avg(rates) as rates'))
                    ->groupBy('product_reviews.product_id')
                    ->orderBy('rates', 'desc');
            }
        }

        return $query;
    }

    public function getPrettySpecification()
    {
        return $this->selectedSpecifications->where('allow_selection', false)
            ->map(function ($selected) {
                if ($selected->type == 'textarea') {
                    $value = $selected->value;
                } elseif (!empty($selected->selectedMultiValues)) {
                    $value = $selected->selectedMultiValues->map(function ($multi) {
                        return $multi->multiValue->title;
                    });
                }
                return [
                    'title' => $selected->specification->title,
                    'type' => $selected->type,
                    'value' => $value
                ];
            })->toArray();


    }

    public function dde()
    {
        return $this->selectedSpecifications->where('allow_selection', true)
            ->where('type', 'multi_value')->map(function ($selectable) {
                return [
                    'title' => $selectable->specification->title,
                    'values' => $selectable->selectedMultiValues->map(function ($multi) {
                        return $multi->multiValue->title;
                    }),
                    //   'name' => $selectable->specification->createName(),
                ];
            })->toArray();
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Api\Comment', 'product_id', 'id');
    }


}
