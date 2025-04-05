<?php

namespace App\Mixins\Cart;

use App\Models\AbandonedCartRuleHistory;
use App\Models\Cart;
use App\Models\AbandonedCartRule;
use App\Models\Product;
use App\Models\Webinar;
use Illuminate\Database\Eloquent\Builder;

class AbandonedCartReminder
{
    private $user;
    protected $totalCartsPrice;
    protected $userTotalCartsPrice;

    public function __construct($user = null)
    {
        $this->user = $user;
    }

    public function sendAbandonedReminders()
    {
        $carts = Cart::query()->get();

        if ($carts->isNotEmpty()) {
            $this->totalCartsPrice = Cart::getCartsTotalPrice($carts);

            foreach ($carts as $cart) {
                $this->user = $cart->user;
                $this->userTotalCartsPrice = Cart::getCartsTotalPrice($carts->where('creator_id', $this->user->id));

                $rules = $this->getRulesByCartItem($cart);

                if (!empty($rules) and count($rules)) {
                    foreach ($rules as $rule) {
                        $this->handleRuleAction($rule);
                    }
                }
            }
        }

        return true;
    }

    private function handleRuleAction($rule)
    {
        $historiesQuery = AbandonedCartRuleHistory::query()->where('cart_rule_id', $rule->id)
            ->where('user_id', $this->user->id)
            ->orderBy('created_at', 'desc');

        $historiesCount = deepClone($historiesQuery)->count();
        $lastHistory = deepClone($historiesQuery)->first();

        $canSendNotification = false;

        if ($historiesCount < 1) {
            $canSendNotification = true;
        } elseif ($rule->repeat_action and $rule->repeat_action_count > $historiesCount) {
            if (!empty($rule->action_cycle) and !empty($lastHistory)) {
                $dueDate = $lastHistory->created_at + ($rule->action_cycle * 60 * 60);

                if ($dueDate <= time()) {
                    $canSendNotification = true;
                }
            } else {
                $canSendNotification = true;
            }
        }

        if ($canSendNotification) {
            if ($rule->action == "send_coupon") {
                $discount = $rule->discount;

                if (!empty($discount)) {
                    $this->getNotificationTemplateAndData($rule, $discount);
                }
            } else {
                $this->getNotificationTemplateAndData($rule);
            }

            AbandonedCartRuleHistory::query()->create([
                'cart_rule_id' => $rule->id,
                'user_id' => $this->user->id,
                'rule_action' => $rule->action,
                'type' => 'auto',
                'created_at' => time()
            ]);
        }

    }

    private function getNotificationTemplateAndData($rule, $discount = null)
    {
        if ($rule->action == "send_coupon") {
            $template = getAbandonedCartSettings("default_cart_coupon_template");

            $notifyOptions = [
                "[u.name]" => $this->user->full_name,
                "[amount]" => $this->userTotalCartsPrice,
                "[discount_title]" => $discount->title,
                "[discount_code]" => $discount->code,
                "[discount_amount]" => ($discount->discount_type == "percentage") ? "{$discount->percent}%" : handlePrice($discount->anount),
            ];
        } else {
            $template = getAbandonedCartSettings("default_cart_reminder");

            $notifyOptions = [
                "[u.name]" => $this->user->full_name,
                "[amount]" => $this->userTotalCartsPrice
            ];
        }

        sendNotification($template, $notifyOptions, $this->user->id, null, 'system', 'single', $template);
    }

    private function getRulesByCartItem($item) // item => Cart
    {
        $targetType = null;
        $itemId = null;
        $itemType = null;
        $categoryId = null;
        $sellerId = null;

        if (!empty($item->webinar_id)) {
            $targetType = 'courses';

            $course = $item->webinar;
            if (!empty($course)) {
                $itemId = $course->id;
                $itemType = $course->type;
                $categoryId = $course->category_id;
                $sellerId = $course->teacher_id;
            }
        } elseif (!empty($item->reserve_meeting_id)) {
            $targetType = 'meetings';

            $meeting = $item->reserveMeeting->meeting;
            if (!empty($meeting)) {
                $itemId = null;
                $itemType = null;
                $categoryId = null;
                $sellerId = $meeting->creator_id;
            }
        } elseif (!empty($item->product_id) or !empty($item->product_order_id)) {
            $targetType = 'store_products';

            $product = !empty($item->product_id) ? $item->product : $item->productOrder->product;
            if (!empty($product)) {
                $itemId = $product->id;
                $itemType = $product->type;
                $categoryId = $product->category_id;
                $sellerId = $product->creator_id;
            }
        } elseif (!empty($item->bundle_id)) {
            $targetType = 'bundles';

            $bundle = $item->bundle;
            if (!empty($bundle)) {
                $itemId = $bundle->id;
                $itemType = $bundle->type;
                $categoryId = $bundle->category_id;
                $sellerId = $bundle->teacher_id;
            }
        }

        if (!empty($targetType)) {
            return $this->getRules($targetType, $itemId, $itemType, $categoryId, $sellerId);
        }

        return null;
    }

    private function getRules($targetType, $itemId = null, $itemType = null, $categoryId = null, $instructorId = null)
    {
        $group = !empty($this->user) ? $this->user->getUserGroup() : null;
        $groupId = !empty($group) ? $group->id : null;
        $userId = !empty($this->user) ? $this->user->id : null;
        $time = time();

        $query = AbandonedCartRule::query();

        if (!empty($groupId)) {
            $query->where(function ($query) use ($groupId) {
                $query->whereDoesntHave('userGroups');
                $query->orWhereHas('usersAndGroups', function ($query) use ($groupId) {
                    $query->where('group_id', $groupId);
                });
            });
        } else {
            $query->whereDoesntHave('userGroups');
        }

        if (!empty($userId)) {
            $query->where(function ($query) use ($userId) {
                $query->whereDoesntHave('users');
                $query->orWhereHas('usersAndGroups', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                });
            });
        } else {
            $query->whereDoesntHave('users');
        }

        $query->where(function ($query) use ($time) {
            $query->whereNull('start_at');
            $query->orWhere('start_at', '<', $time);
        });

        $query->where(function ($query) use ($time) {
            $query->whereNull('end_at');
            $query->orWhere('end_at', '>', $time);
        });

        $query->where(function (Builder $query) use ($targetType, $itemType, $categoryId, $instructorId, $itemId) {
            $query->where('target_type', 'all');

            $query->orWhere(function (Builder $query) use ($targetType, $itemType, $categoryId, $instructorId, $itemId) {
                $query->where('target_type', $targetType);

                if ($targetType == 'courses') {
                    $this->targetCoursesQuery($query, $itemType, $categoryId, $instructorId, $itemId);
                } else if ($targetType == 'store_products') {
                    $this->targetStoreProductsQuery($query, $itemType, $categoryId, $instructorId, $itemId);
                } else if ($targetType == 'bundles') {
                    $this->targetBundlesQuery($query, $categoryId, $instructorId, $itemId);
                } else if ($targetType == 'meetings') {
                    $this->targetMeetingsQuery($query, $instructorId);
                }
            });
        });

        $query->where(function (Builder $query) {
            $query->whereNull('minimum_cart_amount');
            $query->orWhere('minimum_cart_amount', '<=', $this->totalCartsPrice);
        });

        $query->where(function (Builder $query) {
            $query->whereNull('maximum_cart_amount');
            $query->orWhere('maximum_cart_amount', '>', $this->totalCartsPrice);
        });

        $query->where('enable', true);

        return $query->get();
    }

    private function targetCoursesQuery(Builder $query, $courseType, $categoryId, $instructorId, $itemId): Builder
    {
        $courseTypeTarget = ($courseType == Webinar::$webinar) ? 'live_classes' : (($courseType == Webinar::$course) ? 'video_courses' : 'text_courses');

        $query->where(function (Builder $query) use ($courseTypeTarget, $categoryId, $instructorId, $itemId) {
            $query->whereIn('target', ['all_courses', $courseTypeTarget]);

            // Specific Category
            $query->orWhere(function (Builder $query) use ($categoryId) {
                $query->where('target', 'specific_categories');
                $query->whereHas('specificationItems', function ($query) use ($categoryId) {
                    $query->where('category_id', $categoryId);
                });
            });

            // Specific Instructor
            $query->orWhere(function (Builder $query) use ($instructorId) {
                $query->where('target', 'specific_instructors');
                $query->whereHas('specificationItems', function ($query) use ($instructorId) {
                    $query->where('instructor_id', $instructorId);
                });
            });

            // Specific Course
            $query->orWhere(function (Builder $query) use ($itemId) {
                $query->where('target', 'specific_courses');
                $query->whereHas('specificationItems', function ($query) use ($itemId) {
                    $query->where('webinar_id', $itemId);
                });
            });
        });

        return $query;
    }

    private function targetStoreProductsQuery(Builder $query, $productType, $categoryId, $sellerId, $itemId): Builder
    {
        $productTypeTarget = ($productType == Product::$physical) ? 'physical_products' : 'virtual_products';

        $query->where(function (Builder $query) use ($productTypeTarget, $categoryId, $sellerId, $itemId) {
            $query->where('target', 'all_products');
            $query->orWhere('target', $productTypeTarget);

            // Specific Category
            $query->orWhere(function (Builder $query) use ($categoryId) {
                $query->where('target', 'specific_categories');
                $query->whereHas('specificationItems', function ($query) use ($categoryId) {
                    $query->where('category_id', $categoryId);
                });
            });

            // Specific Seller
            $query->orWhere(function (Builder $query) use ($sellerId) {
                $query->where('target', 'specific_sellers');
                $query->whereHas('specificationItems', function ($query) use ($sellerId) {
                    $query->where('seller_id', $sellerId);
                });
            });

            // Specific Product
            $query->orWhere(function (Builder $query) use ($itemId) {
                $query->where('target', 'specific_products');
                $query->whereHas('specificationItems', function ($query) use ($itemId) {
                    $query->where('product_id', $itemId);
                });
            });
        });

        return $query;
    }

    private function targetBundlesQuery(Builder $query, $categoryId, $instructorId, $itemId): Builder
    {
        $query->where(function (Builder $query) use ($categoryId, $instructorId, $itemId) {
            $query->where('target', 'all_bundles');

            // Specific Category
            $query->orWhere(function (Builder $query) use ($categoryId) {
                $query->where('target', 'specific_categories');
                $query->whereHas('specificationItems', function ($query) use ($categoryId) {
                    $query->where('category_id', $categoryId);
                });
            });

            // Specific Seller
            $query->orWhere(function (Builder $query) use ($instructorId) {
                $query->where('target', 'specific_instructors');
                $query->whereHas('specificationItems', function ($query) use ($instructorId) {
                    $query->where('instructor_id', $instructorId);
                });
            });

            // Specific Product
            $query->orWhere(function (Builder $query) use ($itemId) {
                $query->where('target', 'specific_bundles');
                $query->whereHas('specificationItems', function ($query) use ($itemId) {
                    $query->where('bundle_id', $itemId);
                });
            });
        });

        return $query;
    }

    private function targetMeetingsQuery(Builder $query, $instructorId): Builder
    {
        $query->where(function (Builder $query) use ($instructorId) {
            $query->where('target', 'all_meetings');

            // Specific Instructor
            $query->orWhere(function (Builder $query) use ($instructorId) {
                $query->where('target', 'specific_instructors');
                $query->whereHas('specificationItems', function ($query) use ($instructorId) {
                    $query->where('instructor_id', $instructorId);
                });
            });
        });

        return $query;
    }

}
