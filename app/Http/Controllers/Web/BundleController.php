<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\traits\CheckContentLimitationTrait;
use App\Http\Controllers\Web\traits\InstallmentsTrait;
use App\Mixins\Cashback\CashbackRules;
use App\Mixins\Installment\InstallmentPlans;
use App\Models\AdvertisingBanner;
use App\Models\Bundle;
use App\Models\Cart;
use App\Models\Discount;
use App\Models\Favorite;
use App\Models\RewardAccounting;
use App\Models\Sale;
use App\Models\Webinar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BundleController extends Controller
{
    use InstallmentsTrait;
    use CheckContentLimitationTrait;

    public function index($slug)
    {
        $user = null;

        if (auth()->check()) {
            $user = auth()->user();
        }

        $contentLimitation = $this->checkContentLimitation($user);
        if ($contentLimitation != "ok") {
            return $contentLimitation;
        }

        $bundle = Bundle::where('slug', $slug)
            ->with([
                'tickets' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
                'relatedCourses' => function ($query) {
                    $query->whereHas('course', function ($query) {
                        $query->where('status', 'active');
                    });
                },
                'filterOptions',
                'category',
                'teacher',
                'tags',
                'bundleWebinars' => function ($query) {
                    $query->with([
                        'webinar' => function ($query) {
                            $query->where('status', Webinar::$active);
                        }
                    ]);
                },
                'reviews' => function ($query) {
                    $query->where('status', 'active');
                    $query->with([
                        'comments' => function ($query) {
                            $query->where('status', 'active');
                        },
                        'creator' => function ($qu) {
                            $qu->select('id', 'full_name', 'avatar');
                        }
                    ]);
                },
                'comments' => function ($query) {
                    $query->where('status', 'active');
                    $query->whereNull('reply_id');
                    $query->with([
                        'user' => function ($query) {
                            $query->select('id', 'full_name', 'role_name', 'role_id', 'avatar', 'avatar_settings');
                        },
                        'replies' => function ($query) {
                            $query->where('status', 'active');
                            $query->with([
                                'user' => function ($query) {
                                    $query->select('id', 'full_name', 'role_name', 'role_id', 'avatar', 'avatar_settings');
                                }
                            ]);
                        }
                    ]);
                    $query->orderBy('created_at', 'desc');
                },
            ])
            ->withCount([
                'sales' => function ($query) {
                    $query->whereNull('refund_at');
                }
            ])
            ->where('status', 'active')
            ->first();

        if (empty($bundle)) {
            abort(404);
        }


        $installmentLimitation = $this->installmentContentLimitation($user, $bundle->id, 'bundle_id');
        if ($installmentLimitation != "ok") {
            return $installmentLimitation;
        }


        $isFavorite = false;

        if (!empty($user)) {
            $isFavorite = Favorite::where('bundle_id', $bundle->id)
                ->where('user_id', $user->id)
                ->first();
        }

        $hasBought = $bundle->checkUserHasBought($user);
        $canSale = ($bundle->canSale() and !$hasBought);

        $advertisingBanners = AdvertisingBanner::where('published', true)
            ->whereIn('position', ['bundle', 'bundle_sidebar'])
            ->get();

        /* Installments */
        if ($canSale and !empty($bundle->price) and $bundle->price > 0 and getInstallmentsSettings('status') and (empty($user) or $user->enable_installments)) {
            $installmentPlans = new InstallmentPlans($user);
            $installments = $installmentPlans->getPlans('bundles', $bundle->id, $bundle->type, $bundle->category_id, $bundle->teacher_id);
        }

        /* Cashback Rules */
        if ($canSale and !empty($bundle->price) and getFeaturesSettings('cashback_active') and (empty($user) or !$user->disable_cashback)) {
            $cashbackRulesMixin = new CashbackRules($user);
            $cashbackRules = $cashbackRulesMixin->getRules('bundles', $bundle->id, $bundle->type, $bundle->category_id, $bundle->teacher_id);
        }

        $instructorDiscounts = null;

        if (!empty(getFeaturesSettings('frontend_coupons_status'))) {
            $instructorDiscounts = Discount::query()
                ->where(function (Builder $query) use ($bundle) {
                    $query->where('creator_id', $bundle->creator_id);
                    $query->orWhere('creator_id', $bundle->teacher_id);
                })
                ->where(function (Builder $query) use ($bundle) {
                    $query->where('source', 'all');
                    $query->orWhere(function (Builder $query) use ($bundle) {
                        $query->where('source', Discount::$discountSourceBundle);

                        $query->where(function (Builder $query) use ($bundle) {
                            $query->whereHas('discountBundles', function ($query) use ($bundle) {
                                $query->where('bundle_id', $bundle->id);
                            });

                            $query->whereDoesntHave('discountBundles');
                        });
                    });
                })
                ->where('status', 'active')
                ->where('expired_at', '>', time())
                ->get();
        }

        $pageRobot = getPageRobot('bundle_show'); // index

        $data = [
            'pageTitle' => $bundle->title,
            'pageDescription' => $bundle->seo_description,
            'pageRobot' => $pageRobot,
            'bundle' => $bundle,
            'isFavorite' => $isFavorite,
            'hasBought' => $hasBought,
            'user' => $user,
            'advertisingBanners' => $advertisingBanners->where('position', 'bundle'),
            'advertisingBannersSidebar' => $advertisingBanners->where('position', 'bundle_sidebar'),
            'activeSpecialOffer' => $bundle->activeSpecialOffer(),
            'cashbackRules' => $cashbackRules ?? null,
            'installments' => $installments ?? null,
            'instructorDiscounts' => $instructorDiscounts,
        ];

        return view('web.default.bundle.index', $data);
    }

    public function favoriteToggle($slug)
    {
        $userId = auth()->id();
        $bundle = Bundle::where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if (!empty($bundle)) {

            $isFavorite = Favorite::where('bundle_id', $bundle->id)
                ->where('user_id', $userId)
                ->first();

            if (empty($isFavorite)) {
                Favorite::create([
                    'user_id' => $userId,
                    'bundle_id' => $bundle->id,
                    'created_at' => time()
                ]);
            } else {
                $isFavorite->delete();
            }
        }

        return response()->json([], 200);
    }

    public function buyWithPoint($slug)
    {
        if (auth()->check()) {
            $user = auth()->user();

            $bundle = Bundle::where('slug', $slug)
                ->where('status', 'active')
                ->first();

            if (!empty($bundle)) {
                if (empty($bundle->points)) {
                    $toastData = [
                        'title' => '',
                        'msg' => trans('update.can_not_buy_this_bundle_with_point'),
                        'status' => 'error'
                    ];
                    return back()->with(['toast' => $toastData]);
                }

                $availablePoints = $user->getRewardPoints();

                if ($availablePoints < $bundle->points) {
                    $toastData = [
                        'title' => '',
                        'msg' => trans('update.you_have_no_enough_points_for_this_bundle'),
                        'status' => 'error'
                    ];
                    return back()->with(['toast' => $toastData]);
                }

                $checkCourseForSale = checkCourseForSale($bundle, $user);

                if ($checkCourseForSale != 'ok') {
                    return $checkCourseForSale;
                }

                Sale::create([
                    'buyer_id' => $user->id,
                    'seller_id' => $bundle->creator_id,
                    'bundle_id' => $bundle->id,
                    'type' => Sale::$bundle,
                    'payment_method' => Sale::$credit,
                    'amount' => 0,
                    'total_amount' => 0,
                    'created_at' => time(),
                ]);

                RewardAccounting::makeRewardAccounting($user->id, $bundle->points, 'withdraw', null, false, RewardAccounting::DEDUCTION);

                $toastData = [
                    'title' => '',
                    'msg' => trans('update.success_pay_bundle_with_point_msg'),
                    'status' => 'success'
                ];
                return back()->with(['toast' => $toastData]);
            }

            abort(404);
        } else {
            return redirect('/login');
        }
    }

    public function free(Request $request, $slug)
    {
        if (auth()->check()) {
            $user = auth()->user();

            $bundle = Bundle::where('slug', $slug)
                ->where('status', 'active')
                ->first();

            if (!empty($bundle)) {
                $checkCourseForSale = checkCourseForSale($bundle, $user);

                if ($checkCourseForSale != 'ok') {
                    return $checkCourseForSale;
                }

                if (!empty($bundle->price) and $bundle->price > 0) {
                    $toastData = [
                        'title' => trans('cart.fail_purchase'),
                        'msg' => trans('update.bundle_not_free'),
                        'status' => 'error'
                    ];
                    return back()->with(['toast' => $toastData]);
                }

                Sale::create([
                    'buyer_id' => $user->id,
                    'seller_id' => $bundle->creator_id,
                    'bundle_id' => $bundle->id,
                    'type' => Sale::$bundle,
                    'payment_method' => Sale::$credit,
                    'amount' => 0,
                    'total_amount' => 0,
                    'created_at' => time(),
                ]);

                $toastData = [
                    'title' => '',
                    'msg' => trans('cart.success_pay_msg_for_free_course'),
                    'status' => 'success'
                ];
                return back()->with(['toast' => $toastData]);
            }

            abort(404);
        } else {
            return redirect('/login');
        }
    }

    public function directPayment(Request $request)
    {
        $user = auth()->user();

        if (!empty($user) and !empty(getFeaturesSettings('direct_bundles_payment_button_status'))) {
            $this->validate($request, [
                'item_id' => 'required',
                'item_name' => 'nullable',
            ]);

            $data = $request->except('_token');

            $bundleId = $data['item_id'];
            $ticketId = $data['ticket_id'] ?? null;

            $bundle = Bundle::where('id', $bundleId)
                ->where('status', 'active')
                ->first();

            if (!empty($bundle)) {
                $checkCourseForSale = checkCourseForSale($bundle, $user);

                if ($checkCourseForSale != 'ok') {
                    return $checkCourseForSale;
                }

                $fakeCarts = collect();

                $fakeCart = new Cart();
                $fakeCart->creator_id = $user->id;
                $fakeCart->bundle_id = $bundle->id;
                $fakeCart->ticket_id = $ticketId;
                $fakeCart->special_offer_id = null;
                $fakeCart->created_at = time();

                $fakeCarts->add($fakeCart);

                $cartController = new CartController();

                return $cartController->checkout(new Request(), $fakeCarts);
            }
        }

        abort(404);
    }

}
