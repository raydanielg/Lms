<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\traits\ProductBadgeTrait;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Panel\Traits\VideoDemoTrait;
use App\Http\Controllers\Panel\WebinarStatisticController;
use App\Mail\SendNotifications;
use App\Models\Bundle;
use App\Models\BundleFilterOption;
use App\Models\Category;
use App\Models\Gift;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\InstallmentOrder;
use App\Models\Notification;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SpecialOffer;
use App\Models\Tag;
use App\Models\Ticket;
use App\Models\Translation\BundleTranslation;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BundleController extends Controller
{
    use ProductBadgeTrait, VideoDemoTrait;

    public function index(Request $request)
    {
        $this->authorize('admin_bundles_list');

        removeContentLocale();

        $query = Bundle::query();

        $totalBundles = $query->count();
        $totalPendingBundles = deepClone($query)->where('bundles.status', Bundle::$pending)->count();
        $totalSales = deepClone($query)->join('sales', 'bundles.id', '=', 'sales.bundle_id')
            ->select(DB::raw('count(sales.bundle_id) as sales_count, sum(total_amount) as total_amount'))
            ->whereNotNull('sales.bundle_id')
            ->whereNull('sales.refund_at')
            ->first();

        $categories = Category::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $query = $this->handleFilters($query, $request)
            ->with([
                'category',
                'teacher' => function ($qu) {
                    $qu->select('id', 'full_name');
                },
                'sales' => function ($query) {
                    $query->whereNull('refund_at');
                }
            ])
            ->withCount([
                'bundleWebinars'
            ]);

        $bundles = $query->paginate(10);

        foreach ($bundles as $bundle) {
            $giftsIds = Gift::query()->where('bundle_id', $bundle->id)
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('date');
                    $query->orWhere('date', '<', time());
                })
                ->whereHas('sale')
                ->pluck('id')
                ->toArray();

            $sales = Sale::query()
                ->where(function ($query) use ($bundle, $giftsIds) {
                    $query->where('bundle_id', $bundle->id);
                    $query->orWhereIn('gift_id', $giftsIds);
                })
                ->whereNull('refund_at')
                ->get();

            $bundle->sales = $sales;
        }


        $data = [
            'pageTitle' => trans('update.bundles'),
            'bundles' => $bundles,
            'totalBundles' => $totalBundles,
            'totalPendingBundles' => $totalPendingBundles,
            'totalSales' => $totalSales,
            'categories' => $categories,
        ];

        $teacher_ids = $request->get('teacher_ids', null);
        if (!empty($teacher_ids)) {
            $data['teachers'] = User::select('id', 'full_name')->whereIn('id', $teacher_ids)->get();
        }

        return view('admin.bundles.lists', $data);
    }

    private function handleFilters($query, $request)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $title = $request->get('title', null);
        $teacher_ids = $request->get('teacher_ids', null);
        $category_id = $request->get('category_id', null);
        $status = $request->get('status', null);
        $sort = $request->get('sort', null);

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($title)) {
            $query->whereTranslationLike('title', '%' . $title . '%');
        }

        if (!empty($teacher_ids) and count($teacher_ids)) {
            $query->whereIn('teacher_id', $teacher_ids);
        }

        if (!empty($category_id)) {
            $query->where('category_id', $category_id);
        }

        if (!empty($status)) {
            $query->where('bundles.status', $status);
        }

        if (!empty($sort)) {
            switch ($sort) {
                case 'has_discount':
                    $now = time();
                    $bundleIdsHasDiscount = [];

                    $tickets = Ticket::where('start_date', '<', $now)
                        ->where('end_date', '>', $now)
                        ->get();

                    foreach ($tickets as $ticket) {
                        if ($ticket->isValid()) {
                            $bundleIdsHasDiscount[] = $ticket->bundle_id;
                        }
                    }

                    $specialOffersBundleIds = SpecialOffer::where('status', 'active')
                        ->where('from_date', '<', $now)
                        ->where('to_date', '>', $now)
                        ->pluck('bundle_id')
                        ->toArray();

                    $bundleIdsHasDiscount = array_merge($specialOffersBundleIds, $bundleIdsHasDiscount);

                    $query->whereIn('id', $bundleIdsHasDiscount)
                        ->orderBy('created_at', 'desc');
                    break;
                case 'sales_asc':
                    $query->join('sales', 'bundles.id', '=', 'sales.bundle_id')
                        ->select('bundles.*', 'sales.bundle_id', 'sales.refund_at', DB::raw('count(sales.bundle_id) as sales_count'))
                        ->whereNotNull('sales.bundle_id')
                        ->whereNull('sales.refund_at')
                        ->groupBy('sales.bundle_id')
                        ->orderBy('sales_count', 'asc');
                    break;
                case 'sales_desc':
                    $query->join('sales', 'bundles.id', '=', 'sales.bundle_id')
                        ->select('bundles.*', 'sales.bundle_id', 'sales.refund_at', DB::raw('count(sales.bundle_id) as sales_count'))
                        ->whereNotNull('sales.bundle_id')
                        ->whereNull('sales.refund_at')
                        ->groupBy('sales.bundle_id')
                        ->orderBy('sales_count', 'desc');
                    break;

                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;

                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;

                case 'income_asc':
                    $query->join('sales', 'bundles.id', '=', 'sales.bundle_id')
                        ->select('bundles.*', 'sales.bundle_id', 'sales.total_amount', 'sales.refund_at', DB::raw('(sum(sales.total_amount) - (sum(sales.tax) + sum(sales.commission))) as amounts'))
                        ->whereNotNull('sales.bundle_id')
                        ->whereNull('sales.refund_at')
                        ->groupBy('sales.bundle_id')
                        ->orderBy('amounts', 'asc');
                    break;

                case 'income_desc':
                    $query->join('sales', 'bundles.id', '=', 'sales.bundle_id')
                        ->select('bundles.*', 'sales.bundle_id', 'sales.total_amount', 'sales.refund_at', DB::raw('(sum(sales.total_amount) - (sum(sales.tax) + sum(sales.commission))) as amounts'))
                        ->whereNotNull('sales.bundle_id')
                        ->whereNull('sales.refund_at')
                        ->groupBy('sales.bundle_id')
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
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }


        return $query;
    }

    public function create()
    {
        $this->authorize('admin_bundles_create');

        removeContentLocale();

        $categories = Category::where('parent_id', null)->get();

        $data = [
            'pageTitle' => trans('update.new_bundle'),
            'categories' => $categories
        ];

        return view('admin.bundles.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_bundles_create');

        $this->validate($request, [
            'title' => 'required|max:255',
            'slug' => 'max:255|unique:bundles,slug',
            'thumbnail' => 'required',
            'image_cover' => 'required',
            'description' => 'required',
            'teacher_id' => 'required|exists:users,id',
            'category_id' => 'required',
        ]);

        $data = $request->all();

        if (empty($data['slug'])) {
            $data['slug'] = Bundle::makeSlug($data['title']);
        }

        $data = $this->handleVideoDemoData($request, $data, "bundle_demo_" . time());

        $bundle = Bundle::create([
            'slug' => $data['slug'],
            'teacher_id' => $data['teacher_id'],
            'creator_id' => $data['teacher_id'],
            'thumbnail' => $data['thumbnail'],
            'image_cover' => $data['image_cover'],
            'video_demo' => $data['video_demo'],
            'video_demo_source' => $data['video_demo'] ? $data['video_demo_source'] : null,
            'subscribe' => !empty($data['subscribe']) ? true : false,
            'certificate' => !empty($data['certificate']) ? true : false,
            'points' => $data['points'] ?? null,
            'price' => $data['price'],
            'access_days' => $data['access_days'] ?? null,
            'category_id' => $data['category_id'],
            'message_for_reviewer' => $data['message_for_reviewer'] ?? null,
            'status' => Bundle::$pending,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        if ($bundle) {
            BundleTranslation::updateOrCreate([
                'bundle_id' => $bundle->id,
                'locale' => mb_strtolower($data['locale']),
            ], [
                'title' => $data['title'],
                'description' => $data['description'],
                'seo_description' => $data['seo_description'],
            ]);
        }

        $filters = $request->get('filters', null);
        if (!empty($filters) and is_array($filters)) {
            BundleFilterOption::where('bundle_id', $bundle->id)->delete();

            foreach ($filters as $filter) {
                BundleFilterOption::create([
                    'bundle_id' => $bundle->id,
                    'filter_option_id' => $filter
                ]);
            }
        }

        if (!empty($request->get('tags'))) {
            $tags = explode(',', $request->get('tags'));
            Tag::where('bundle_id', $bundle->id)->delete();

            foreach ($tags as $tag) {
                Tag::create([
                    'bundle_id' => $bundle->id,
                    'title' => $tag,
                ]);
            }
        }

        return redirect(getAdminPanelUrl() . '/bundles/' . $bundle->id . '/edit?locale=' . $data['locale']);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('admin_bundles_edit');

        $bundle = Bundle::where('id', $id)
            ->with([
                'tickets',
                'faqs',
                'category' => function ($query) {
                    $query->with(['filters' => function ($query) {
                        $query->with('options');
                    }]);
                },
                'tags',
                'bundleWebinars'
            ])
            ->first();

        if (empty($bundle)) {
            abort(404);
        }

        $locale = $request->get('locale', app()->getLocale());
        storeContentLocale($locale, $bundle->getTable(), $bundle->id);

        $categories = Category::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $tags = $bundle->tags->pluck('title')->toArray();

        $data = [
            'pageTitle' => trans('admin/main.edit') . ' | ' . $bundle->title,
            'categories' => $categories,
            'bundle' => $bundle,
            'bundleCategoryFilters' => !empty($bundle->category) ? $bundle->category->filters : null,
            'bundleFilterOptions' => $bundle->filterOptions->pluck('filter_option_id')->toArray(),
            'tickets' => $bundle->tickets,
            'faqs' => $bundle->faqs,
            'bundleTags' => $tags,
            'bundleWebinars' => $bundle->bundleWebinars,
        ];

        return view('admin.bundles.create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_bundles_edit');
        $data = $request->all();

        $bundle = Bundle::find($id);
        $isDraft = (!empty($data['draft']) and $data['draft'] == 1);
        $reject = (!empty($data['draft']) and $data['draft'] == 'reject');
        $publish = (!empty($data['draft']) and $data['draft'] == 'publish');

        $rules = [
            'title' => 'required|max:255',
            'slug' => 'max:255|unique:bundles,slug,' . $bundle->id,
            'thumbnail' => 'required',
            'image_cover' => 'required',
            'description' => 'required',
            'teacher_id' => 'required|exists:users,id',
            'category_id' => 'required',
        ];

        $this->validate($request, $rules);

        if (!empty($data['teacher_id'])) {
            $teacher = User::findOrFail($data['teacher_id']);
            $creator = $bundle->creator;

            if (empty($teacher) or ($creator->isOrganization() and ($teacher->organ_id != $creator->id and $teacher->id != $creator->id))) {
                $toastData = [
                    'title' => trans('public.request_failed'),
                    'msg' => trans('admin/main.is_not_the_teacher_of_this_organization'),
                    'status' => 'error'
                ];
                return back()->with(['toast' => $toastData]);
            }
        }


        if (empty($data['slug'])) {
            $data['slug'] = Bundle::makeSlug($data['title']);
        }

        $data['status'] = $publish ? Bundle::$active : ($reject ? Bundle::$inactive : ($isDraft ? Bundle::$isDraft : Bundle::$pending));
        $data['updated_at'] = time();
        $data['subscribe'] = !empty($data['subscribe']) ? true : false;
        $data['certificate'] = !empty($data['certificate']) ? true : false;

        if ($data['category_id'] != $bundle->category_id) {
            BundleFilterOption::where('bundle_id', $bundle->id)->delete();
        }

        $filters = $request->get('filters', null);
        if (!empty($filters) and is_array($filters)) {
            BundleFilterOption::where('bundle_id', $bundle->id)->delete();

            foreach ($filters as $filter) {
                BundleFilterOption::create([
                    'bundle_id' => $bundle->id,
                    'filter_option_id' => $filter
                ]);
            }
        }

        if (!empty($request->get('tags'))) {
            $tags = explode(',', $request->get('tags'));
            Tag::where('bundle_id', $bundle->id)->delete();

            foreach ($tags as $tag) {
                Tag::create([
                    'bundle_id' => $bundle->id,
                    'title' => $tag,
                ]);
            }
        }

        // Product Badge
        $this->handleProductBadges($bundle, $data);

        unset($data['_token'],
            $data['current_step'],
            $data['draft'],
            $data['get_next'],
            $data['partners'],
            $data['tags'],
            $data['filters'],
            $data['ajax'],
            $data['product_badges']
        );

        $data = $this->handleVideoDemoData($request, $data, "bundle_demo_" . time());

        $bundle->update([
            'slug' => $data['slug'],
            'teacher_id' => $data['teacher_id'],
            'thumbnail' => $data['thumbnail'],
            'image_cover' => $data['image_cover'],
            'video_demo' => $data['video_demo'],
            'video_demo_source' => $data['video_demo'] ? $data['video_demo_source'] : null,
            'subscribe' => $data['subscribe'],
            'certificate' => $data['certificate'],
            'points' => $data['points'] ?? null,
            'price' => $data['price'],
            'access_days' => $data['access_days'] ?? null,
            'category_id' => $data['category_id'],
            'message_for_reviewer' => $data['message_for_reviewer'] ?? null,
            'status' => $data['status'],
            'updated_at' => time(),
        ]);

        if ($bundle) {
            BundleTranslation::updateOrCreate([
                'bundle_id' => $bundle->id,
                'locale' => mb_strtolower($data['locale']),
            ], [
                'title' => $data['title'],
                'description' => $data['description'],
                'seo_description' => $data['seo_description'],
            ]);
        }

        $notifyOptions = [
            '[item_title]' => $bundle->title,
        ];

        if ($publish) {
            sendNotification('bundle_approved', $notifyOptions, $bundle->teacher_id);

            /*$createClassesReward = RewardAccounting::calculateScore(Reward::CREATE_CLASSES);
            RewardAccounting::makeRewardAccounting(
                $bundle->creator_id,
                $createClassesReward,
                Reward::CREATE_CLASSES,
                $bundle->id,
                true
            );*/

        } elseif ($reject) {
            sendNotification('bundle_rejected', $notifyOptions, $bundle->teacher_id);
        }

        removeContentLocale();

        return back();
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_bundles_delete');

        $bundle = Bundle::find($id);

        if (!empty($bundle)) {
            $bundle->delete();
        }

        return redirect(getAdminPanelUrl() . '/bundles');
    }

    public function studentsLists(Request $request, $id)
    {
        $this->authorize('admin_webinar_students_lists');

        $bundle = Bundle::where('id', $id)
            ->with([
                'teacher' => function ($qu) {
                    $qu->select('id', 'full_name');
                }
            ])
            ->first();


        if (!empty($bundle)) {
            $giftsIds = Gift::query()->where('bundle_id', $bundle->id)
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('date');
                    $query->orWhere('date', '<', time());
                })
                ->whereHas('sale')
                ->pluck('id')
                ->toArray();

            $installmentSalesIds = [];
            $installmentOrders = InstallmentOrder::query()
                ->where('bundle_id', $bundle->id)
                ->where('status', 'open')
                ->get();

            foreach ($installmentOrders as $installmentOrder) {

                $salesId = $installmentOrder->payments->pluck('sale_id')->toArray();
                $installmentSalesIds = array_merge($installmentSalesIds, $salesId);
            }

            $query = User::join('sales', 'sales.buyer_id', 'users.id')
                ->leftJoin('webinar_reviews', function ($query) use ($bundle) {
                    $query->on('webinar_reviews.creator_id', 'users.id')
                        ->where('webinar_reviews.bundle_id', $bundle->id);
                })
                ->select('users.*', 'webinar_reviews.rates', 'sales.gift_id', DB::raw('sales.created_at as purchase_date'))
                ->where(function ($query) use ($bundle, $giftsIds, $installmentSalesIds) {
                    $query->where('sales.bundle_id', $bundle->id);
                    $query->orWhereIn('sales.gift_id', $giftsIds);
                    $query->orWhereIn('sales.id', $installmentSalesIds);
                })
                ->groupBy('sales.buyer_id')
                ->whereNull('sales.refund_at');

            $students = $this->studentsListsFilters($bundle, $query, $request)
                ->orderBy('sales.created_at', 'desc')
                ->paginate(10);

            $userGroups = Group::where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->get();

            $totalExpireStudents = 0;
            if (!empty($bundle->access_days)) {
                $accessTimestamp = $bundle->access_days * 24 * 60 * 60;

                $totalExpireStudents = User::join('sales', 'sales.buyer_id', 'users.id')
                    ->select('users.*', DB::raw('sales.created_at as purchase_date'))
                    ->where(function ($query) use ($bundle, $giftsIds) {
                        $query->where('sales.bundle_id', $bundle->id);
                        $query->orWhereIn('sales.gift_id', $giftsIds);
                    })
                    ->whereRaw('sales.created_at + ? < ?', [$accessTimestamp, time()])
                    ->whereNull('sales.refund_at')
                    ->count();
            }

            $bundleWebinars = $bundle->bundleWebinars;

            $webinarStatisticController = new WebinarStatisticController();

            foreach ($students as $key => $student) {
                $learnings = 0;
                $webinarCount = 0;

                foreach ($bundleWebinars as $bundleWebinar) {
                    if (!empty($bundleWebinar->webinar)) {
                        $webinarCount += 1;
                        $learnings += $webinarStatisticController->getCourseProgressForStudent($bundleWebinar->webinar, $student->id);
                    }
                }

                $learnings = ($learnings > 0 and $webinarCount > 0) ? round($learnings / $webinarCount, 2) : 0;

                if (!empty($student->gift_id)) {
                    $gift = Gift::query()->where('id', $student->gift_id)->first();

                    if (!empty($gift)) {
                        $receipt = $gift->receipt;

                        if (!empty($receipt)) {
                            $receipt->rates = $student->rates;
                            $receipt->access_to_purchased_item = $student->access_to_purchased_item;
                            $receipt->sale_id = $student->sale_id;
                            $receipt->purchase_date = $student->purchase_date;
                            $receipt->learning = $learnings;

                            $students[$key] = $receipt;
                        } else { /* Gift recipient who has not registered yet */
                            $newUser = new User();
                            $newUser->full_name = $gift->name;
                            $newUser->email = $gift->email;
                            $newUser->rates = 0;
                            $newUser->access_to_purchased_item = $student->access_to_purchased_item;
                            $newUser->sale_id = $student->sale_id;
                            $newUser->purchase_date = $student->purchase_date;
                            $newUser->learning = 0;

                            $students[$key] = $newUser;
                        }
                    }
                } else {
                    $student->learning = $learnings;
                }
            }

            $roles = Role::all();

            $data = [
                'pageTitle' => trans('admin/main.students'),
                'bundle' => $bundle,
                'students' => $students,
                'userGroups' => $userGroups,
                'roles' => $roles,
                'totalStudents' => $students->total(),
                'totalActiveStudents' => $students->total() - $totalExpireStudents,
                'totalExpireStudents' => $totalExpireStudents,
            ];

            return view('admin.bundles.students', $data);
        }

        abort(404);
    }

    private function studentsListsFilters($bundle, $query, $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $full_name = $request->get('full_name');
        $sort = $request->get('sort');
        $group_id = $request->get('group_id');
        $role_id = $request->get('role_id');
        $status = $request->get('status');

        $query = fromAndToDateFilter($from, $to, $query, 'sales.created_at');

        if (!empty($full_name)) {
            $query->where('users.full_name', 'like', "%$full_name%");
        }

        if (!empty($sort)) {
            if ($sort == 'rate_asc') {
                $query->orderBy('webinar_reviews.rates', 'asc');
            }

            if ($sort == 'rate_desc') {
                $query->orderBy('webinar_reviews.rates', 'desc');
            }
        }

        if (!empty($group_id)) {
            $userIds = GroupUser::where('group_id', $group_id)->pluck('user_id')->toArray();

            $query->whereIn('users.id', $userIds);
        }

        if (!empty($role_id)) {
            $query->where('users.role_id', $role_id);
        }

        if (!empty($status)) {
            if ($status == 'expire' and !empty($bundle->access_days)) {
                $accessTimestamp = $bundle->access_days * 24 * 60 * 60;

                $query->whereRaw('sales.created_at + ? < ?', [$accessTimestamp, time()]);
            }
        }

        return $query;
    }

    public function notificationToStudents($id)
    {
        $this->authorize('admin_webinar_notification_to_students');

        $bundle = Bundle::findOrFail($id);

        $data = [
            'pageTitle' => trans('notification.send_notification'),
            'bundle' => $bundle
        ];

        return view('admin.bundles.send-notification-to-course-students', $data);
    }


    public function sendNotificationToStudents(Request $request, $id)
    {
        $this->authorize('admin_webinar_notification_to_students');

        $this->validate($request, [
            'title' => 'required|string',
            'message' => 'required|string',
        ]);

        $data = $request->all();

        $bundle = Bundle::where('id', $id)
            ->with([
                'sales' => function ($query) {
                    $query->whereNull('refund_at');
                    $query->with([
                        'buyer'
                    ]);
                }
            ])
            ->first();

        if (!empty($bundle)) {
            foreach ($bundle->sales as $sale) {
                if (!empty($sale->buyer)) {
                    $user = $sale->buyer;

                    Notification::create([
                        'user_id' => $user->id,
                        'group_id' => null,
                        'sender_id' => auth()->id(),
                        'title' => $data['title'],
                        'message' => $data['message'],
                        'sender' => Notification::$AdminSender,
                        'type' => 'single',
                        'created_at' => time()
                    ]);

                    if (!empty($user->email) and env('APP_ENV') == 'production') {
                        \Mail::to($user->email)->send(new SendNotifications(['title' => $data['title'], 'message' => $data['message']]));
                    }
                }
            }

            $toastData = [
                'title' => trans('public.request_success'),
                'msg' => trans('update.the_notification_was_successfully_sent_to_n_students', ['count' => count($bundle->sales)]),
                'status' => 'success'
            ];

            return redirect(getAdminPanelUrl("/bundles/{$bundle->id}/students"))->with(['toast' => $toastData]);
        }

        abort(404);
    }

    public function search(Request $request)
    {
        $term = $request->get('term');

        $option = $request->get('option', null);

        $query = Bundle::select('id')
            ->whereTranslationLike('title', "%$term%");

        $bundles = $query->get();

        return response()->json($bundles, 200);
    }


    public function approve(Request $request, $id)
    {
        $this->authorize('admin_bundles_edit');

        $bundle = Bundle::query()->findOrFail($id);

        $bundle->update([
            'status' => Bundle::$active
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.bundle_status_changes_to_approved'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/bundles"))->with(['toast' => $toastData]);
    }

    public function reject(Request $request, $id)
    {
        $this->authorize('admin_bundles_edit');

        $bundle = Bundle::query()->findOrFail($id);

        $bundle->update([
            'status' => Bundle::$inactive
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.bundle_status_changes_to_rejected'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/bundles"))->with(['toast' => $toastData]);
    }

    public function unpublish(Request $request, $id)
    {
        $this->authorize('admin_bundles_edit');

        $bundle = Bundle::query()->findOrFail($id);

        $bundle->update([
            'status' => Bundle::$pending
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.bundle_status_changes_to_unpublished'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/bundles"))->with(['toast' => $toastData]);
    }

}
