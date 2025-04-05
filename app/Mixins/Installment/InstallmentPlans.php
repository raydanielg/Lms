<?php

namespace App\Mixins\Installment;

use App\Models\Installment;
use App\Models\Product;
use App\Models\Webinar;
use Illuminate\Database\Eloquent\Builder;

class InstallmentPlans
{
    private $user;

    public function __construct($user = null)
    {
        if (empty($user)) {
            $user = auth()->user();
        }

        $this->user = $user;
    }

    public function getPlans($targetType, $itemId = null, $itemType = null, $categoryId = null, $instructorId = null, $sellerId = null)
    {
        $group = !empty($this->user) ? $this->user->getUserGroup() : null;
        $groupId = !empty($group) ? $group->id : null;
        $time = time();

        $query = Installment::query();

        if (!empty($groupId)) {
            $query->where(function ($query) use ($groupId) {
                $query->whereDoesntHave('userGroups');
                $query->orWhereHas('userGroups', function ($query) use ($groupId) {
                    $query->where('group_id', $groupId);
                });
            });
        }

        $query->where(function ($query) use ($time) {
            $query->whereNull('start_date');
            $query->orWhere('start_date', '<', $time);
        });

        $query->where(function ($query) use ($time) {
            $query->whereNull('end_date');
            $query->orWhere('end_date', '>', $time);
        });

        $query->where(function (Builder $query) use ($targetType, $itemType, $categoryId, $instructorId, $itemId, $sellerId) {
            $query->where('target_type', 'all');

            $query->orWhere(function (Builder $query) use ($targetType, $itemType, $categoryId, $instructorId, $itemId, $sellerId) {
                $query->where('target_type', $targetType);

                if ($targetType == 'courses') {
                    $this->targetCoursesQuery($query, $itemType, $categoryId, $instructorId, $itemId);
                } else if ($targetType == 'store_products') {
                    $this->targetStoreProductsQuery($query, $itemType, $categoryId, $sellerId, $itemId);
                } else if ($targetType == 'bundles') {
                    $this->targetBundlesQuery($query, $categoryId, $instructorId, $itemId);
                } else if ($targetType == 'meetings') {
                    $this->targetMeetingsQuery($query, $instructorId);
                } else if ($targetType == 'registration_packages') {
                    $this->targetRegistrationPackagesQuery($query, $itemId);
                } else if ($targetType == 'subscription_packages') {
                    $this->targetSubscriptionPackagesQuery($query, $itemId);
                }
            });
        });

        $query->where('enable', true);

        return $query->get();
    }


    private function targetCoursesQuery(Builder $query, $courseType, $categoryId, $instructorId, $itemId): Builder
    {
        $courseTypeTarget = ($courseType == Webinar::$webinar) ? 'live_classes' : (($courseType == Webinar::$course) ? 'video_courses' : 'text_courses');

        $query->where(function (Builder $query) use ($courseTypeTarget, $categoryId, $instructorId, $itemId) {
            $query->where('target', 'all_courses');
            $query->orWhere('target', $courseTypeTarget);

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

    private function targetRegistrationPackagesQuery(Builder $query, $itemId): Builder
    {
        $query->where(function (Builder $query) use ($itemId) {
            $query->where('target', 'all_packages');

            // Specific Package
            $query->orWhere(function (Builder $query) use ($itemId) {
                $query->where('target', 'specific_packages');
                $query->whereHas('specificationItems', function ($query) use ($itemId) {
                    $query->where('registration_package_id', $itemId);
                });
            });
        });

        return $query;
    }

    private function targetSubscriptionPackagesQuery(Builder $query, $itemId): Builder
    {
        $query->where(function (Builder $query) use ($itemId) {
            $query->where('target', 'all_packages');

            // Specific Package
            $query->orWhere(function (Builder $query) use ($itemId) {
                $query->where('target', 'specific_packages');
                $query->whereHas('specificationItems', function ($query) use ($itemId) {
                    $query->where('subscribe_id', $itemId);
                });
            });
        });

        return $query;
    }

}
