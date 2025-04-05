<?php

namespace App\Http\Controllers\Admin;

use App\Exports\WebinarsExport;
use App\Http\Controllers\Admin\traits\ProductBadgeTrait;
use App\Http\Controllers\Admin\traits\WebinarChangeCreator;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Panel\Traits\VideoDemoTrait;
use App\Http\Controllers\Panel\WebinarStatisticController;
use App\Mail\SendNotifications;
use App\Models\BundleWebinar;
use App\Models\Category;
use App\Models\File;
use App\Models\Gift;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\InstallmentOrder;
use App\Models\Notification;
use App\Models\Quiz;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Session;
use App\Models\SpecialOffer;
use App\Models\Tag;
use App\Models\TextLesson;
use App\Models\Ticket;
use App\Models\Translation\WebinarTranslation;
use App\Models\WebinarChapter;
use App\Models\WebinarChapterItem;
use App\Models\WebinarFilterOption;
use App\Models\WebinarPartnerTeacher;
use App\User;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class WebinarController extends Controller
{
    use WebinarChangeCreator, ProductBadgeTrait, VideoDemoTrait;


    public function index(Request $request)
    {
        $this->authorize('admin_webinars_list');

        removeContentLocale();

        $type = $request->get('type', 'webinar');
        $query = Webinar::where('webinars.type', $type);

        $totalWebinars = $query->count();
        $totalPendingWebinars = deepClone($query)->where('webinars.status', 'pending')->count();
        $totalDurations = deepClone($query)->sum('duration');
        $totalSales = deepClone($query)->join('sales', 'webinars.id', '=', 'sales.webinar_id')
            ->select(DB::raw('count(sales.webinar_id) as sales_count'))
            ->whereNotNull('sales.webinar_id')
            ->whereNull('sales.refund_at')
            ->first();

        $categories = Category::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $inProgressWebinars = 0;
        if ($type == 'webinar') {
            $inProgressWebinars = $this->getInProgressWebinarsCount();
        }

        $query = $this->filterWebinar($query, $request)
            ->with([
                'category',
                'teacher' => function ($qu) {
                    $qu->select('id', 'full_name');
                },
                'sales' => function ($query) {
                    $query->whereNull('refund_at');
                }
            ]);

        $webinars = $query->paginate(10);

        if ($request->get('status', null) == 'active_finished') {
            foreach ($webinars as $key => $webinar) {
                if ($webinar->last_date > time()) { // is in progress
                    unset($webinars[$key]);
                }
            }
        }

        foreach ($webinars as $webinar) {
            $giftsIds = Gift::query()->where('webinar_id', $webinar->id)
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('date');
                    $query->orWhere('date', '<', time());
                })
                ->whereHas('sale')
                ->pluck('id')
                ->toArray();

            $sales = Sale::query()
                ->where(function ($query) use ($webinar, $giftsIds) {
                    $query->where('webinar_id', $webinar->id);
                    $query->orWhereIn('gift_id', $giftsIds);
                })
                ->whereNull('refund_at')
                ->get();

            $webinar->sales = $sales;
        }


        $data = [
            'pageTitle' => trans('admin/pages/webinars.webinars_list_page_title'),
            'webinars' => $webinars,
            'totalWebinars' => $totalWebinars,
            'totalPendingWebinars' => $totalPendingWebinars,
            'totalDurations' => $totalDurations,
            'totalSales' => !empty($totalSales) ? $totalSales->sales_count : 0,
            'categories' => $categories,
            'inProgressWebinars' => $inProgressWebinars,
            'classesType' => $type,
        ];

        $teacher_ids = $request->get('teacher_ids', null);
        if (!empty($teacher_ids)) {
            $data['teachers'] = User::select('id', 'full_name')->whereIn('id', $teacher_ids)->get();
        }

        return view('admin.webinars.lists', $data);
    }

    private function filterWebinar($query, $request)
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
            $time = time();

            switch ($status) {
                case 'active_not_conducted':
                    $query->where('webinars.status', 'active')
                        ->where('start_date', '>', $time);
                    break;
                case 'active_in_progress':
                    $query->where('webinars.status', 'active')
                        ->where('start_date', '<=', $time)
                        ->join('sessions', 'webinars.id', '=', 'sessions.webinar_id')
                        ->select('webinars.*', 'sessions.date', DB::raw('max(`date`) as last_date'))
                        ->groupBy('sessions.webinar_id')
                        ->where('sessions.date', '>', $time);
                    break;
                case 'active_finished':
                    $query->where('webinars.status', 'active')
                        ->where('start_date', '<=', $time)
                        ->join('sessions', 'webinars.id', '=', 'sessions.webinar_id')
                        ->select('webinars.*', 'sessions.date', DB::raw('max(`date`) as last_date'))
                        ->groupBy('sessions.webinar_id');
                    break;
                default:
                    $query->where('webinars.status', $status);
                    break;
            }
        }

        if (!empty($sort)) {
            switch ($sort) {
                case 'has_discount':
                    $now = time();
                    $webinarIdsHasDiscount = [];

                    $tickets = Ticket::where('start_date', '<', $now)
                        ->where('end_date', '>', $now)
                        ->get();

                    foreach ($tickets as $ticket) {
                        if ($ticket->isValid()) {
                            $webinarIdsHasDiscount[] = $ticket->webinar_id;
                        }
                    }

                    $specialOffersWebinarIds = SpecialOffer::where('status', 'active')
                        ->where('from_date', '<', $now)
                        ->where('to_date', '>', $now)
                        ->pluck('webinar_id')
                        ->toArray();

                    $webinarIdsHasDiscount = array_merge($specialOffersWebinarIds, $webinarIdsHasDiscount);

                    $query->whereIn('id', $webinarIdsHasDiscount)
                        ->orderBy('created_at', 'desc');
                    break;
                case 'sales_asc':
                    $query->join('sales', 'webinars.id', '=', 'sales.webinar_id')
                        ->select('webinars.*', 'sales.webinar_id', 'sales.refund_at', DB::raw('count(sales.webinar_id) as sales_count'))
                        ->whereNotNull('sales.webinar_id')
                        ->whereNull('sales.refund_at')
                        ->groupBy('sales.webinar_id')
                        ->orderBy('sales_count', 'asc');
                    break;
                case 'sales_desc':
                    $query->join('sales', 'webinars.id', '=', 'sales.webinar_id')
                        ->select('webinars.*', 'sales.webinar_id', 'sales.refund_at', DB::raw('count(sales.webinar_id) as sales_count'))
                        ->whereNotNull('sales.webinar_id')
                        ->whereNull('sales.refund_at')
                        ->groupBy('sales.webinar_id')
                        ->orderBy('sales_count', 'desc');
                    break;

                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;

                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;

                case 'income_asc':
                    $query->join('sales', 'webinars.id', '=', 'sales.webinar_id')
                        ->select('webinars.*', 'sales.webinar_id', 'sales.total_amount', 'sales.refund_at', DB::raw('(sum(sales.total_amount) - (sum(sales.tax) + sum(sales.commission))) as amounts'))
                        ->whereNotNull('sales.webinar_id')
                        ->whereNull('sales.refund_at')
                        ->groupBy('sales.webinar_id')
                        ->orderBy('amounts', 'asc');
                    break;

                case 'income_desc':
                    $query->join('sales', 'webinars.id', '=', 'sales.webinar_id')
                        ->select('webinars.*', 'sales.webinar_id', 'sales.total_amount', 'sales.refund_at', DB::raw('(sum(sales.total_amount) - (sum(sales.tax) + sum(sales.commission))) as amounts'))
                        ->whereNotNull('sales.webinar_id')
                        ->whereNull('sales.refund_at')
                        ->groupBy('sales.webinar_id')
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

                case 'public_courses':
                    $query->where('private', false);
                    $query->orderBy('created_at', 'desc');
                    break;

                case 'courses_private':
                    $query->where('private', true);
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }


        return $query;
    }

    private function getInProgressWebinarsCount()
    {
        $count = 0;
        $webinars = Webinar::where('type', 'webinar')
            ->where('status', 'active')
            ->where('start_date', '<=', time())
            ->whereHas('sessions')
            ->get();

        foreach ($webinars as $webinar) {
            if ($webinar->isProgressing()) {
                $count += 1;
            }
        }

        return $count;
    }

    public function create()
    {
        $this->authorize('admin_webinars_create');

        removeContentLocale();

        $teachers = User::where('role_name', Role::$teacher)->get();
        $categories = Category::where('parent_id', null)->get();

        $data = [
            'pageTitle' => trans('admin/main.webinar_new_page_title'),
            'teachers' => $teachers,
            'categories' => $categories
        ];

        return view('admin.webinars.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_webinars_create');

        $this->validate($request, [
            'type' => 'required|in:webinar,course,text_lesson',
            'title' => 'required|max:255',
            'slug' => 'max:255|unique:webinars,slug',
            'thumbnail' => 'required',
            'image_cover' => 'required',
            'description' => 'required',
            'teacher_id' => 'required|exists:users,id',
            'category_id' => 'required',
            'duration' => 'required|numeric',
            'start_date' => 'required_if:type,webinar',
            'capacity' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();


        if (!empty($data['capacity']) and !empty($data['sales_count_number']) and $data['sales_count_number'] > $data['capacity']) {
            return back()->withErrors([
                'sales_count_number' => [
                    trans('validation.digits_between', [
                        'attribute' => trans('update.sales_count_number'),
                        'min' => 0,
                        'max' => $data['capacity']
                    ])
                ]
            ]);
        }

        if ($data['type'] != Webinar::$webinar) {
            $data['start_date'] = null;
        }

        if (!empty($data['start_date']) and $data['type'] == Webinar::$webinar) {
            if (empty($data['timezone']) or !getFeaturesSettings('timezone_in_create_webinar')) {
                $data['timezone'] = getTimezone();
            }

            $startDate = convertTimeToUTCzone($data['start_date'], $data['timezone']);

            $data['start_date'] = $startDate->getTimestamp();
        }

        if (empty($data['slug'])) {
            $data['slug'] = Webinar::makeSlug($data['title']);
        }

        $data = $this->handleVideoDemoData($request, $data, "course_demo_" . time());

        $data['price'] = !empty($data['price']) ? convertPriceToDefaultCurrency($data['price']) : null;
        $data['organization_price'] = !empty($data['organization_price']) ? convertPriceToDefaultCurrency($data['organization_price']) : null;

        $webinar = Webinar::create([
            'type' => $data['type'],
            'slug' => $data['slug'],
            'teacher_id' => $data['teacher_id'],
            'creator_id' => $data['teacher_id'],
            'thumbnail' => $data['thumbnail'],
            'image_cover' => $data['image_cover'],
            'video_demo' => $data['video_demo'],
            'video_demo_source' => $data['video_demo'] ? $data['video_demo_source'] : null,
            'sales_count_number' => $data['sales_count_number'] ?? null,
            'capacity' => $data['capacity'] ?? null,
            'start_date' => (!empty($data['start_date'])) ? $data['start_date'] : null,
            'timezone' => $data['timezone'] ?? null,
            'duration' => $data['duration'] ?? null,
            'support' => !empty($data['support']) ? true : false,
            'certificate' => !empty($data['certificate']) ? true : false,
            'downloadable' => !empty($data['downloadable']) ? true : false,
            'partner_instructor' => !empty($data['partner_instructor']) ? true : false,
            'subscribe' => !empty($data['subscribe']) ? true : false,
            'private' => !empty($data['private']) ? true : false,
            'forum' => !empty($data['forum']) ? true : false,
            'enable_waitlist' => (!empty($data['enable_waitlist'])),
            'access_days' => $data['access_days'] ?? null,
            'price' => $data['price'],
            'organization_price' => $data['organization_price'] ?? null,
            'points' => $data['points'] ?? null,
            'category_id' => $data['category_id'],
            'message_for_reviewer' => $data['message_for_reviewer'] ?? null,
            'status' => Webinar::$pending,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        if ($webinar) {
            WebinarTranslation::updateOrCreate([
                'webinar_id' => $webinar->id,
                'locale' => mb_strtolower($data['locale']),
            ], [
                'title' => $data['title'],
                'description' => $data['description'],
                'seo_description' => $data['seo_description'],
            ]);
        }

        $filters = $request->get('filters', null);
        if (!empty($filters) and is_array($filters)) {
            WebinarFilterOption::where('webinar_id', $webinar->id)->delete();
            foreach ($filters as $filter) {
                WebinarFilterOption::create([
                    'webinar_id' => $webinar->id,
                    'filter_option_id' => $filter
                ]);
            }
        }

        if (!empty($request->get('tags'))) {
            $tags = explode(',', $request->get('tags'));
            Tag::where('webinar_id', $webinar->id)->delete();

            foreach ($tags as $tag) {
                Tag::create([
                    'webinar_id' => $webinar->id,
                    'title' => $tag,
                ]);
            }
        }

        if (!empty($request->get('partner_instructor')) and !empty($request->get('partners'))) {
            WebinarPartnerTeacher::where('webinar_id', $webinar->id)->delete();

            foreach ($request->get('partners') as $partnerId) {
                WebinarPartnerTeacher::create([
                    'webinar_id' => $webinar->id,
                    'teacher_id' => $partnerId,
                ]);
            }
        }


        return redirect(getAdminPanelUrl() . '/webinars/' . $webinar->id . '/edit?locale=' . $data['locale']);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $webinar = Webinar::where('id', $id)
            ->with([
                'tickets',
                'sessions',
                'files',
                'faqs',
                'category' => function ($query) {
                    $query->with(['filters' => function ($query) {
                        $query->with('options');
                    }]);
                },
                'filterOptions',
                'prerequisites',
                'quizzes' => function ($query) {
                    $query->with([
                        'quizQuestions' => function ($query) {
                            $query->orderBy('order', 'asc');
                        }
                    ]);
                },
                'webinarPartnerTeacher' => function ($query) {
                    $query->with(['teacher' => function ($query) {
                        $query->select('id', 'full_name');
                    }]);
                },
                'tags',
                'textLessons',
                'assignments',
                'chapters' => function ($query) {
                    $query->orderBy('order', 'asc');
                    $query->with([
                        'chapterItems' => function ($query) {
                            $query->orderBy('order', 'asc');

                            $query->with([
                                'quiz' => function ($query) {
                                    $query->with([
                                        'quizQuestions' => function ($query) {
                                            $query->orderBy('order', 'asc');
                                        }
                                    ]);
                                }
                            ]);
                        }
                    ]);
                },
            ])
            ->first();

        if (empty($webinar)) {
            abort(404);
        }

        $locale = $request->get('locale', getDefaultLocale());
        storeContentLocale($locale, $webinar->getTable(), $webinar->id);

        $categories = Category::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $teacherQuizzes = Quiz::where('webinar_id', null)
            ->where('creator_id', $webinar->teacher_id)
            ->get();

        $tags = $webinar->tags->pluck('title')->toArray();

        $data = [
            'pageTitle' => trans('admin/main.edit') . ' | ' . $webinar->title,
            'categories' => $categories,
            'webinar' => $webinar,
            'webinarCategoryFilters' => !empty($webinar->category) ? $webinar->category->filters : null,
            'webinarFilterOptions' => $webinar->filterOptions->pluck('filter_option_id')->toArray(),
            'tickets' => $webinar->tickets,
            'chapters' => $webinar->chapters,
            'sessions' => $webinar->sessions,
            'files' => $webinar->files,
            'textLessons' => $webinar->textLessons,
            'faqs' => $webinar->faqs,
            'assignments' => $webinar->assignments,
            'teacherQuizzes' => $teacherQuizzes,
            'prerequisites' => $webinar->prerequisites,
            'webinarQuizzes' => $webinar->quizzes,
            'webinarPartnerTeacher' => $webinar->webinarPartnerTeacher,
            'webinarTags' => $tags,
            'defaultLocale' => getDefaultLocale(),
        ];

        return view('admin.webinars.create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');
        $data = $request->all();

        $webinar = Webinar::find($id);
        $isDraft = (!empty($data['draft']) and $data['draft'] == 1);
        $reject = (!empty($data['draft']) and $data['draft'] == 'reject');
        $publish = (!empty($data['draft']) and $data['draft'] == 'publish');

        $rules = [
            'type' => 'required|in:webinar,course,text_lesson',
            'title' => 'required|max:255',
            'slug' => 'max:255|unique:webinars,slug,' . $webinar->id,
            'thumbnail' => 'required',
            'image_cover' => 'required',
            'description' => 'required',
            'teacher_id' => 'required|exists:users,id',
            'category_id' => 'required',
            'price' => 'nullable|numeric|min:0',
        ];

        if ($webinar->isWebinar()) {
            $rules['start_date'] = 'required|date';
            $rules['duration'] = 'required';
            $rules['capacity'] = 'nullable|numeric|min:0';
        }

        $this->validate($request, $rules);

        if (!empty($data['capacity']) and !empty($data['sales_count_number']) and $data['sales_count_number'] > $data['capacity']) {
            return back()->withErrors([
                'sales_count_number' => [
                    trans('validation.digits_between', [
                        'attribute' => trans('update.sales_count_number'),
                        'min' => 0,
                        'max' => $data['capacity']
                    ])
                ]
            ]);
        }

        if (!empty($data['teacher_id'])) {
            $teacher = User::find($data['teacher_id']);
            $creator = !empty($data['organ_id']) ? User::find($data['organ_id']) : $webinar->creator;

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
            $data['slug'] = Webinar::makeSlug($data['title']);
        }

        $data['status'] = $publish ? Webinar::$active : ($reject ? Webinar::$inactive : ($isDraft ? Webinar::$isDraft : Webinar::$pending));
        $data['updated_at'] = time();

        if (!empty($data['start_date']) and $webinar->type == 'webinar') {
            if (empty($data['timezone']) or !getFeaturesSettings('timezone_in_create_webinar')) {
                $data['timezone'] = getTimezone();
            }

            $startDate = convertTimeToUTCzone($data['start_date'], $data['timezone']);

            $data['start_date'] = $startDate->getTimestamp();
        } else {
            $data['start_date'] = null;
        }


        $data['support'] = !empty($data['support']) ? true : false;
        $data['certificate'] = !empty($data['certificate']) ? true : false;
        $data['downloadable'] = !empty($data['downloadable']) ? true : false;
        $data['partner_instructor'] = !empty($data['partner_instructor']) ? true : false;
        $data['subscribe'] = !empty($data['subscribe']) ? true : false;
        $data['forum'] = !empty($data['forum']) ? true : false;
        $data['private'] = !empty($data['private']) ? true : false;
        $data['enable_waitlist'] = (!empty($data['enable_waitlist']));

        if (empty($data['partner_instructor'])) {
            WebinarPartnerTeacher::where('webinar_id', $webinar->id)->delete();
            unset($data['partners']);
        }

        if ($data['category_id'] !== $webinar->category_id) {
            WebinarFilterOption::where('webinar_id', $webinar->id)->delete();
        }

        $filters = $request->get('filters', null);
        if (!empty($filters) and is_array($filters)) {
            WebinarFilterOption::where('webinar_id', $webinar->id)->delete();
            foreach ($filters as $filter) {
                WebinarFilterOption::create([
                    'webinar_id' => $webinar->id,
                    'filter_option_id' => $filter
                ]);
            }
        }

        if (!empty($request->get('tags'))) {
            $tags = explode(',', $request->get('tags'));
            Tag::where('webinar_id', $webinar->id)->delete();

            foreach ($tags as $tag) {
                Tag::create([
                    'webinar_id' => $webinar->id,
                    'title' => $tag,
                ]);
            }
        }

        if (!empty($request->get('partner_instructor')) and !empty($request->get('partners'))) {
            WebinarPartnerTeacher::where('webinar_id', $webinar->id)->delete();

            foreach ($request->get('partners') as $partnerId) {
                WebinarPartnerTeacher::create([
                    'webinar_id' => $webinar->id,
                    'teacher_id' => $partnerId,
                ]);
            }
        }

        // Product Badge
        $this->handleProductBadges($webinar, $data);

        unset($data['_token'],
            $data['current_step'],
            $data['draft'],
            $data['get_next'],
            $data['partners'],
            $data['tags'],
            $data['filters'],
            $data['ajax'],
            $data['product_badges'],
        );

        $data = $this->handleVideoDemoData($request, $data, "course_demo_" . time());

        $newCreatorId = !empty($data['organ_id']) ? $data['organ_id'] : $data['teacher_id'];
        $changedCreator = ($webinar->creator_id != $newCreatorId);

        $data['price'] = !empty($data['price']) ? convertPriceToDefaultCurrency($data['price']) : null;
        $data['organization_price'] = !empty($data['organization_price']) ? convertPriceToDefaultCurrency($data['organization_price']) : null;

        $webinar->update([
            'slug' => $data['slug'],
            'creator_id' => $newCreatorId,
            'teacher_id' => $data['teacher_id'],
            'type' => $data['type'],
            'thumbnail' => $data['thumbnail'],
            'image_cover' => $data['image_cover'],
            'video_demo' => $data['video_demo'],
            'video_demo_source' => $data['video_demo'] ? $data['video_demo_source'] : null,
            'capacity' => $data['capacity'] ?? null,
            'sales_count_number' => $data['sales_count_number'] ?? null,
            'start_date' => $data['start_date'],
            'timezone' => $data['timezone'] ?? null,
            'duration' => $data['duration'] ?? null,
            'support' => $data['support'],
            'certificate' => $data['certificate'],
            'private' => $data['private'],
            'enable_waitlist' => $data['enable_waitlist'],
            'downloadable' => $data['downloadable'],
            'partner_instructor' => $data['partner_instructor'],
            'subscribe' => $data['subscribe'],
            'forum' => $data['forum'],
            'access_days' => $data['access_days'] ?? null,
            'price' => $data['price'],
            'organization_price' => $data['organization_price'] ?? null,
            'category_id' => $data['category_id'],
            'points' => $data['points'] ?? null,
            'message_for_reviewer' => $data['message_for_reviewer'] ?? null,
            'status' => $data['status'],
            'updated_at' => time(),
        ]);

        if ($webinar) {
            WebinarTranslation::updateOrCreate([
                'webinar_id' => $webinar->id,
                'locale' => mb_strtolower($data['locale']),
            ], [
                'title' => $data['title'],
                'description' => $data['description'],
                'seo_description' => $data['seo_description'],
            ]);
        }

        if ($publish) {
            sendNotification('course_approve', ['[c.title]' => $webinar->title], $webinar->teacher_id);

            $createClassesReward = RewardAccounting::calculateScore(Reward::CREATE_CLASSES);
            RewardAccounting::makeRewardAccounting(
                $webinar->creator_id,
                $createClassesReward,
                Reward::CREATE_CLASSES,
                $webinar->id,
                true
            );

        } elseif ($reject) {
            sendNotification('course_reject', ['[c.title]' => $webinar->title], $webinar->teacher_id);
        }

        if ($changedCreator) {
            $this->webinarChangedCreator($webinar);
        }

        removeContentLocale();

        return back();
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_webinars_delete');

        $webinar = Webinar::query()->findOrFail($id);

        $webinar->delete();

        return redirect(getAdminPanelUrl() . '/webinars');
    }

    public function approve(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $webinar = Webinar::query()->findOrFail($id);

        $webinar->update([
            'status' => Webinar::$active
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.course_status_changes_to_approved'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl() . '/webinars')->with(['toast' => $toastData]);
    }

    public function reject(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $webinar = Webinar::query()->findOrFail($id);

        $webinar->update([
            'status' => Webinar::$inactive
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.course_status_changes_to_rejected'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl() . '/webinars')->with(['toast' => $toastData]);
    }

    public function unpublish(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $webinar = Webinar::query()->findOrFail($id);

        $webinar->update([
            'status' => Webinar::$pending
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.course_status_changes_to_unpublished'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl() . '/webinars')->with(['toast' => $toastData]);
    }

    public function search(Request $request)
    {
        $term = $request->get('term');

        $option = $request->get('option', null);

        $query = Webinar::select('id')
            ->whereTranslationLike('title', "%$term%");

        if (!empty($option) and $option == 'just_webinar') {
            $query->where('type', Webinar::$webinar);
            $query->where('status', Webinar::$active);
        }

        $webinar = $query->get();

        return response()->json($webinar, 200);
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('admin_webinars_export_excel');

        $query = Webinar::query();

        $query = $this->filterWebinar($query, $request)
            ->with(['teacher' => function ($qu) {
                $qu->select('id', 'full_name');
            }, 'sales']);

        $webinars = $query->get();

        $webinarExport = new WebinarsExport($webinars);

        return Excel::download($webinarExport, 'webinars.xlsx');
    }

    public function studentsLists(Request $request, $id)
    {
        $this->authorize('admin_webinar_students_lists');

        $webinar = Webinar::where('id', $id)
            ->with([
                'teacher' => function ($qu) {
                    $qu->select('id', 'full_name');
                },
                'chapters' => function ($query) {
                    $query->where('status', 'active');
                },
                'sessions' => function ($query) {
                    $query->where('status', 'active');
                },
                'assignments' => function ($query) {
                    $query->where('status', 'active');
                },
                'quizzes' => function ($query) {
                    $query->where('status', 'active');
                },
                'files' => function ($query) {
                    $query->where('status', 'active');
                },
            ])
            ->first();


        if (!empty($webinar)) {
            $giftsIds = Gift::query()->where('webinar_id', $webinar->id)
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
                ->where('webinar_id', $webinar->id)
                ->where('status', 'open')
                ->get();

            foreach ($installmentOrders as $installmentOrder) {

                $salesId = $installmentOrder->payments->pluck('sale_id')->toArray();
                $installmentSalesIds = array_merge($installmentSalesIds, $salesId);
            }

            $query = User::join('sales', 'sales.buyer_id', 'users.id')
                ->leftJoin('webinar_reviews', function ($query) use ($webinar) {
                    $query->on('webinar_reviews.creator_id', 'users.id')
                        ->where('webinar_reviews.webinar_id', $webinar->id);
                })
                ->select('users.*', 'webinar_reviews.rates', 'sales.access_to_purchased_item', 'sales.id as sale_id', 'sales.gift_id', DB::raw('min(sales.created_at) as purchase_date'))
                ->where(function ($query) use ($webinar, $giftsIds, $installmentSalesIds) {
                    $query->where('sales.webinar_id', $webinar->id);
                    $query->orWhereIn('sales.gift_id', $giftsIds);
                    $query->orWhereIn('sales.id', $installmentSalesIds);
                })
                ->groupBy('sales.buyer_id')
                ->whereNull('sales.refund_at');

            $students = $this->studentsListsFilters($webinar, $query, $request)
                ->orderBy('sales.created_at', 'desc')
                ->paginate(10);

            $userGroups = Group::where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->get();

            $totalExpireStudents = 0;
            if (!empty($webinar->access_days)) {
                $accessTimestamp = $webinar->access_days * 24 * 60 * 60;

                $totalExpireStudents = User::join('sales', 'sales.buyer_id', 'users.id')
                    ->select('users.*', DB::raw('sales.created_at as purchase_date'))
                    ->where(function ($query) use ($webinar, $giftsIds) {
                        $query->where('sales.webinar_id', $webinar->id);
                        $query->orWhereIn('sales.gift_id', $giftsIds);
                    })
                    ->whereRaw('sales.created_at + ? < ?', [$accessTimestamp, time()])
                    ->whereNull('sales.refund_at')
                    ->count();
            }

            $webinarStatisticController = new WebinarStatisticController();

            $allStudentsIds = User::join('sales', 'sales.buyer_id', 'users.id')
                ->select('users.*', DB::raw('sales.created_at as purchase_date'))
                ->where(function ($query) use ($webinar, $giftsIds) {
                    $query->where('sales.webinar_id', $webinar->id);
                    $query->orWhereIn('sales.gift_id', $giftsIds);
                })
                ->whereNull('sales.refund_at')
                ->pluck('id')
                ->toArray();

            $learningPercents = [];
            foreach ($allStudentsIds as $studentsId) {
                $learningPercents[$studentsId] = $webinarStatisticController->getCourseProgressForStudent($webinar, $studentsId);
            }

            foreach ($students as $key => $student) {
                if (!empty($student->gift_id)) {
                    $gift = Gift::query()->where('id', $student->gift_id)->first();

                    if (!empty($gift)) {
                        $receipt = $gift->receipt;

                        if (!empty($receipt)) {
                            $receipt->rates = $student->rates;
                            $receipt->access_to_purchased_item = $student->access_to_purchased_item;
                            $receipt->sale_id = $student->sale_id;
                            $receipt->purchase_date = $student->purchase_date;
                            $receipt->learning = $webinarStatisticController->getCourseProgressForStudent($webinar, $receipt->id);

                            $learningPercents[$student->id] = $receipt->learning;

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
                    $student->learning = !empty($learningPercents[$student->id]) ? $learningPercents[$student->id] : 0;
                }
            }

            $roles = Role::all();

            $data = [
                'pageTitle' => trans('admin/main.students'),
                'webinar' => $webinar,
                'students' => $students,
                'userGroups' => $userGroups,
                'roles' => $roles,
                'totalStudents' => $students->total(),
                'totalActiveStudents' => $students->total() - $totalExpireStudents,
                'totalExpireStudents' => $totalExpireStudents,
                'averageLearning' => count($learningPercents) ? round(array_sum($learningPercents) / count($learningPercents), 2) : 0,
            ];

            return view('admin.webinars.students', $data);
        }

        abort(404);
    }

    private function studentsListsFilters($webinar, $query, $request)
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
            if ($status == 'expire' and !empty($webinar->access_days)) {
                $accessTimestamp = $webinar->access_days * 24 * 60 * 60;

                $query->whereRaw('sales.created_at + ? < ?', [$accessTimestamp, time()]);
            }
        }

        return $query;
    }

    public function notificationToStudents($id)
    {
        $this->authorize('admin_webinar_notification_to_students');

        $webinar = Webinar::findOrFail($id);

        $data = [
            'pageTitle' => trans('notification.send_notification'),
            'webinar' => $webinar
        ];

        return view('admin.webinars.send-notification-to-course-students', $data);
    }


    public function sendNotificationToStudents(Request $request, $id)
    {
        $this->authorize('admin_webinar_notification_to_students');

        $this->validate($request, [
            'title' => 'required|string',
            'message' => 'required|string',
        ]);

        $data = $request->all();

        $webinar = Webinar::where('id', $id)
            ->with([
                'sales' => function ($query) {
                    $query->whereNull('refund_at');
                    $query->with([
                        'buyer'
                    ]);
                }
            ])
            ->first();

        if (!empty($webinar)) {
            foreach ($webinar->sales as $sale) {
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
                'msg' => trans('update.the_notification_was_successfully_sent_to_n_students', ['count' => count($webinar->sales)]),
                'status' => 'success'
            ];

            return redirect(getAdminPanelUrl("/webinars/{$webinar->id}/students"))->with(['toast' => $toastData]);
        }

        abort(404);
    }

    public function orderItems(Request $request)
    {
        $this->authorize('admin_webinars_edit');
        $data = $request->all();

        $validator = Validator::make($data, [
            'items' => 'required',
            'table' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $tableName = $data['table'];
        $itemIds = explode(',', $data['items']);

        if (!is_array($itemIds) and !empty($itemIds)) {
            $itemIds = [$itemIds];
        }

        if (!empty($itemIds) and is_array($itemIds) and count($itemIds)) {
            switch ($tableName) {
                case 'tickets':
                    foreach ($itemIds as $order => $id) {
                        Ticket::where('id', $id)
                            ->update(['order' => ($order + 1)]);
                    }
                    break;
                case 'sessions':
                    foreach ($itemIds as $order => $id) {
                        Session::where('id', $id)
                            ->update(['order' => ($order + 1)]);
                    }
                    break;
                case 'files':
                    foreach ($itemIds as $order => $id) {
                        File::where('id', $id)
                            ->update(['order' => ($order + 1)]);
                    }
                    break;
                case 'text_lessons':
                    foreach ($itemIds as $order => $id) {
                        TextLesson::where('id', $id)
                            ->update(['order' => ($order + 1)]);
                    }
                    break;
                case 'webinar_chapters':
                    foreach ($itemIds as $order => $id) {
                        WebinarChapter::where('id', $id)
                            ->update(['order' => ($order + 1)]);
                    }
                    break;
                case 'webinar_chapter_items':
                    foreach ($itemIds as $order => $id) {
                        WebinarChapterItem::where('id', $id)
                            ->update(['order' => ($order + 1)]);
                    }
                case 'bundle_webinars':
                    foreach ($itemIds as $order => $id) {
                        BundleWebinar::where('id', $id)
                            ->update(['order' => ($order + 1)]);
                    }
                    break;
            }
        }

        return response()->json([
            'title' => trans('public.request_success'),
            'msg' => trans('update.items_sorted_successful')
        ]);
    }


    public function getContentItemByLocale(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

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

        $webinar = Webinar::where('id', $id)->first();

        if (!empty($webinar)) {

            $itemId = $data['item_id'];
            $locale = $data['locale'];
            $relation = $data['relation'];

            if (!empty($webinar->$relation)) {
                $item = $webinar->$relation->where('id', $itemId)->first();

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
}
