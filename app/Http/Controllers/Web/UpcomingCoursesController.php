<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\traits\CheckContentLimitationTrait;
use App\Models\AdvertisingBanner;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\UpcomingCourse;
use App\Models\UpcomingCourseFilterOption;
use App\Models\UpcomingCourseFollower;
use App\Models\UpcomingCourseReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpcomingCoursesController extends Controller
{
    use CheckContentLimitationTrait;

    public function index(Request $request)
    {
        $query = UpcomingCourse::query()
            ->where('status', UpcomingCourse::$active);

        $upcomingCoursesCount = deepClone($query)->count();

        $query = $this->handleFilters($request, $query);

        $upcomingCourses = $query->paginate(9);

        $categories = Category::whereNull('parent_id')
            ->with([
                'subCategories' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
            ])
            ->get();

        $selectedCategory = null;

        if (!empty($request->get('category_id'))) {
            $selectedCategory = Category::where('id', $request->get('category_id'))->first();
        }


        $seoSettings = getSeoMetas('upcoming_courses_lists');
        $pageTitle = $seoSettings['title'] ?? '';
        $pageDescription = $seoSettings['description'] ?? '';
        $pageRobot = getPageRobot('upcoming_courses_lists');

        $data = [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'pageRobot' => $pageRobot,
            'categoriesLists' => $categories,
            'selectedCategory' => $selectedCategory,
            'upcomingCourses' => $upcomingCourses,
            'upcomingCoursesCount' => $upcomingCoursesCount,
        ];

        return view(getTemplate() . '.upcoming_courses.lists', $data);
    }

    private function handleFilters(Request $request, $query)
    {
        $free = $request->get('free');
        $released = $request->get('released');
        $sort = $request->get('sort');
        $type = $request->get('type');
        $moreOptions = $request->get('moreOptions');
        $categoryId = $request->get('category_id', null);
        $filterOption = $request->get('filter_option', null);


        if (!empty($free)) {
            $query->where(function ($query) {
                $query->whereNull('price');
                $query->orWhere('price', '<', '1');
            });
        }

        if (!empty($released)) {
            $query->whereNotNull('webinar_id');
        }

        if (!empty($type) and count($type)) {
            $query->whereIn('type', $type);
        }

        if (!empty($moreOptions) and count($moreOptions)) {
            if (in_array('supported_courses', $moreOptions)) {
                $query->where('support', true);
            }

            if (in_array('quiz_included', $moreOptions)) {
                $query->where('include_quizzes', true);
            }

            if (in_array('certificate_included', $moreOptions)) {
                $query->where('certificate', true);
            }
        }


        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if (!empty($filterOption) and is_array($filterOption)) {
            $upcomingIdsFilterOptions = UpcomingCourseFilterOption::whereIn('filter_option_id', $filterOption)
                ->pluck('upcoming_course_id')
                ->toArray();

            $upcomingIdsFilterOptions = array_unique($upcomingIdsFilterOptions);

            $query->whereIn('id', $upcomingIdsFilterOptions);
        }


        if (!empty($sort)) {
            switch ($sort) {
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'earliest_publish_date':
                    $query->orderBy('publish_date', 'asc');
                    break;
                case 'farthest_publish_date':
                    $query->orderBy('publish_date', 'desc');
                    break;
                case 'highest_price':
                    $query->orderBy('price', 'desc');
                    break;
                case 'lowest_price':
                    $query->orderBy('price', 'asc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    public function show(Request $request, $slug)
    {
        $user = null;

        if (auth()->check()) {
            $user = auth()->user();
        }


        $contentLimitation = $this->checkContentLimitation($user);
        if ($contentLimitation != "ok") {
            return $contentLimitation;
        }

        $upcomingCourse = UpcomingCourse::query()
            ->where('slug', $slug)
            ->where('status', UpcomingCourse::$active)
            ->with([
                'tags',
                'followers',
                'faqs' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
                'extraDescriptions' => function ($query) {
                    $query->orderBy('order', 'asc');
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
            ->first();

        if (!empty($upcomingCourse)) {
            $isFavorite = false;
            $followed = false;

            if (!empty($user)) {
                $isFavorite = Favorite::where('upcoming_course_id', $upcomingCourse->id)
                    ->where('user_id', $user->id)
                    ->first();

                $followed = UpcomingCourseFollower::query()
                    ->where('upcoming_course_id', $upcomingCourse->id)
                    ->where('user_id', $user->id)
                    ->first();
            }

            $followingUsersCount = UpcomingCourseFollower::query()->where('upcoming_course_id', $upcomingCourse->id)->count();
            $followingUsers = UpcomingCourseFollower::query()
                ->where('upcoming_course_id', $upcomingCourse->id)
                ->inRandomOrder()
                ->take(3)
                ->with([
                    'user' => function ($query) {
                        $query->select('id', 'full_name', 'role_name', 'role_id', 'avatar', 'avatar_settings');
                    }
                ])
                ->get();


            $advertisingBanners = AdvertisingBanner::where('published', true)
                ->whereIn('position', ['upcoming_course', 'upcoming_course_sidebar'])
                ->get();


            $pageRobot = getPageRobot('upcoming_course_show'); // index

            $data = [
                'pageTitle' => $upcomingCourse->title,
                'pageDescription' => $upcomingCourse->seo_description,
                'pageRobot' => $pageRobot,
                'upcomingCourse' => $upcomingCourse,
                'isFavorite' => $isFavorite,
                'followed' => $followed,
                'advertisingBanners' => $advertisingBanners->where('position', 'upcoming_course'),
                'advertisingBannersSidebar' => $advertisingBanners->where('position', 'upcoming_course_sidebar'),
                'followingUsers' => $followingUsers,
                'followingUsersCount' => $followingUsersCount,
            ];

            return view('web.default.upcoming_courses.show', $data);
        }

        abort(404);
    }

    public function toggleFollow(Request $request, $slug)
    {
        $user = auth()->user();
        $upcomingCourse = UpcomingCourse::query()
            ->where('slug', $slug)
            ->where('status', UpcomingCourse::$active)
            ->first();

        if (!empty($user) and !empty($upcomingCourse)) {

            if (in_array($user->id, [$upcomingCourse->teacher_id, $upcomingCourse->creator_id])) {
                $toastData = [
                    'title' => trans('public.request_failed'),
                    'msg' => trans('update.cant_follow_your_upcoming_course'),
                    'status' => 'error'
                ];
                return back()->with(['toast' => $toastData]);
            }

            $follow = UpcomingCourseFollower::query()
                ->where('upcoming_course_id', $upcomingCourse->id)
                ->where('user_id', $user->id)
                ->first();

            $add = false;

            if (!empty($follow)) {
                $follow->delete();
            } else {
                $add = true;

                UpcomingCourseFollower::query()->create([
                    'upcoming_course_id' => $upcomingCourse->id,
                    'user_id' => $user->id,
                    'created_at' => time()
                ]);

                $notifyOptions = [
                    '[u.name]' => $user->full_name,
                    '[item_title]' => $upcomingCourse->title,
                ];
                sendNotification("upcoming_course_followed", $notifyOptions, $upcomingCourse->teacher_id);
            }

            return response()->json([
                'code' => 200,
                'msg' => $add ? trans('update.the_course_has_been_added_to_your_follow_list') : trans('update.the_course_has_been_removed_from_your_follow_list')
            ]);
        }

        abort(422);
    }

    public function favorite(Request $request, $slug)
    {
        $user = auth()->user();
        $upcomingCourse = UpcomingCourse::query()
            ->where('slug', $slug)
            ->where('status', UpcomingCourse::$active)
            ->first();

        if (!empty($user) and !empty($upcomingCourse)) {
            $isFavorite = Favorite::where('upcoming_course_id', $upcomingCourse->id)
                ->where('user_id', $user->id)
                ->first();

            if (empty($isFavorite)) {
                Favorite::create([
                    'user_id' => $user->id,
                    'upcoming_course_id' => $upcomingCourse->id,
                    'created_at' => time()
                ]);
            } else {
                $isFavorite->delete();
            }

            return response()->json([
                'code' => 200,
            ]);
        }

        abort(422);
    }

    public function report(Request $request, $id)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'reason' => 'required|string',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $upcomingCourse = UpcomingCourse::query()
            ->where('id', $id)
            ->where('status', UpcomingCourse::$active)
            ->first();

        if (!empty($user) and !empty($upcomingCourse)) {

            UpcomingCourseReport::create([
                'user_id' => $user->id,
                'upcoming_course_id' => $upcomingCourse->id,
                'reason' => $data['reason'],
                'message' => $data['message'],
                'created_at' => time()
            ]);

            $notifyOptions = [
                '[u.name]' => $user->full_name,
                '[content_type]' => trans('update.upcoming_course')
            ];
            sendNotification("new_report_item_for_admin", $notifyOptions, 1);

            return response()->json([
                'code' => 200
            ], 200);
        }

        abort(422);
    }
}
