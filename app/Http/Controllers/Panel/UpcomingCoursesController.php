<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Panel\Traits\VideoDemoTrait;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Tag;
use App\Models\Translation\UpcomingCourseTranslation;
use App\Models\UpcomingCourse;
use App\Models\UpcomingCourseFilterOption;
use App\Models\UpcomingCourseFollower;
use App\Models\Webinar;
use App\Models\WebinarExtraDescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpcomingCoursesController extends Controller
{
    use VideoDemoTrait;

    public function __construct()
    {
        if (empty(getFeaturesSettings('upcoming_courses_status'))) {
            abort(404);
        }
    }

    public function index(Request $request)
    {
        $this->authorize("panel_upcoming_courses_lists");

        $user = auth()->user();

        $query = UpcomingCourse::query()
            ->where(function ($query) use ($user) {
                $query->where('creator_id', $user->id)
                    ->orWhere('teacher_id', $user->id);
            });

        $totalCourses = deepClone($query)->count();
        $releasedCourses = deepClone($query)->whereNotNull('webinar_id')->count();
        $notReleased = deepClone($query)->whereNull('webinar_id')->count();
        $ids = deepClone($query)->pluck('id')->toArray();
        $followers = UpcomingCourseFollower::query()->whereIn('upcoming_course_id', $ids)->count();

        $onlyNotReleasedCourses = $request->get('only_not_released_courses');
        if (!empty($onlyNotReleasedCourses)) {
            $query->whereNull('webinar_id');
        }

        $upcomingCourses = $query
            ->withCount([
                'followers'
            ])
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.my_upcoming_courses'),
            'totalCourses' => $totalCourses,
            'releasedCourses' => $releasedCourses,
            'notReleased' => $notReleased,
            'followers' => $followers,
            'upcomingCourses' => $upcomingCourses
        ];

        return view(getTemplate() . '.panel.upcoming_courses.lists', $data);
    }

    public function create()
    {
        $this->authorize("panel_upcoming_courses_create");

        $user = auth()->user();
        $teachers = null;
        $isOrganization = $user->isOrganization();

        if ($isOrganization) {
            $teachers = $user->getOrganizationTeachers()->get();
        }

        $data = [
            'pageTitle' => trans('update.new_upcoming_course'),
            'currentStep' => 1,
            'userLanguages' => getUserLanguagesLists(),
            'isOrganization' => $isOrganization,
            'teachers' => $teachers,
        ];

        return view(getTemplate() . '.panel.upcoming_courses.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize("panel_upcoming_courses_create");

        $user = auth()->user();

        $rules = [
            'type' => 'required|in:webinar,course,text_lesson',
            'title' => 'required|max:255',
            'thumbnail' => 'required',
            'image_cover' => 'required',
            'description' => 'required',
        ];

        $this->validate($request, $rules);

        $data = $request->all();
        $data = $this->handleVideoDemoData($request, $data, "upcoming_course_demo_" . time());

        $upcomingCourse = UpcomingCourse::query()->create([
            'creator_id' => $user->id,
            'teacher_id' => $user->isTeacher() ? $user->id : (!empty($data['teacher_id']) ? $data['teacher_id'] : $user->id),
            'slug' => UpcomingCourse::makeSlug($data['title']),
            'type' => $data['type'],
            'thumbnail' => $data['thumbnail'],
            'image_cover' => $data['image_cover'],
            'video_demo' => $data['video_demo'],
            'video_demo_source' => $data['video_demo'] ? $data['video_demo_source'] : null,
            'status' => ((!empty($data['draft']) and $data['draft'] == 1) or (!empty($data['get_next']) and $data['get_next'] == 1)) ? UpcomingCourse::$isDraft : UpcomingCourse::$pending,
            'created_at' => time(),
        ]);

        if (!empty($upcomingCourse)) {
            UpcomingCourseTranslation::query()->updateOrCreate([
                'upcoming_course_id' => $upcomingCourse->id,
                'locale' => mb_strtolower($data['locale']),
            ], [
                'title' => $data['title'],
                'description' => $data['description'],
                'seo_description' => $data['seo_description'],
            ]);

            $notifyOptions = [
                '[u.name]' => $user->full_name,
                '[item_title]' => $upcomingCourse->title,
            ];
            sendNotification("upcoming_course_submission", $notifyOptions, $user->id);
            sendNotification("upcoming_course_submission_for_admin", $notifyOptions, 1);


            $url = '/panel/upcoming_courses';
            if ($data['get_next'] == 1) {
                $url = '/panel/upcoming_courses/' . $upcomingCourse->id . '/step/2';
            }

            return redirect($url);
        }

        abort(500);
    }

    public function edit(Request $request, $id, $step = 1)
    {
        $this->authorize("panel_upcoming_courses_create");

        $user = auth()->user();

        $isOrganization = $user->isOrganization();
        $locale = $request->get('locale', app()->getLocale());

        $data = [
            'currentStep' => $step,
            'isOrganization' => $isOrganization,
            'userLanguages' => getUserLanguagesLists(),
            'locale' => mb_strtolower($locale),
            'defaultLocale' => getDefaultLocale(),
        ];

        $query = UpcomingCourse::query()->where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('creator_id', $user->id)
                    ->orWhere('teacher_id', $user->id);
            });

        if ($step == 1) {
            $data['teachers'] = $user->getOrganizationTeachers()->get();
        } elseif ($step == 2) {
            $query->with([
                'category' => function ($query) {
                    $query->with(['filters' => function ($query) {
                        $query->with('options');
                    }]);
                },
                'filterOptions',
                'tags',
            ]);

            $categories = Category::where('parent_id', null)
                ->with('subCategories')
                ->get();

            $data['categories'] = $categories;
        } elseif ($step == 3) {
            $query->with([
                'faqs' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
                'extraDescriptions' => function ($query) {
                    $query->orderBy('order', 'asc');
                }
            ]);
        }

        $upcomingCourse = $query->first();

        if (empty($upcomingCourse)) {
            abort(404);
        }

        $data['upcomingCourse'] = $upcomingCourse;
        $data['pageTitle'] = trans('public.edit') . ' | ' . $upcomingCourse->title;

        $definedLanguage = [];
        if ($upcomingCourse->translations) {
            $definedLanguage = $upcomingCourse->translations->pluck('locale')->toArray();
        }
        $data['definedLanguage'] = $definedLanguage;

        if ($step == 2) {
            $data['upcomingCourseTags'] = $upcomingCourse->tags->pluck('title')->toArray();

            $upcomingCourseCategoryFilters = !empty($upcomingCourse->category) ? $upcomingCourse->category->filters : [];

            if (empty($upcomingCourse->category) and !empty($request->old('category_id'))) {
                $category = Category::where('id', $request->old('category_id'))->first();

                if (!empty($category)) {
                    $upcomingCourseCategoryFilters = $category->filters;
                }
            }

            $data['upcomingCourseCategoryFilters'] = $upcomingCourseCategoryFilters;
        }

        return view(getTemplate() . '.panel.upcoming_courses.create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize("panel_upcoming_courses_create");

        $user = auth()->user();

        $rules = [];
        $data = $request->all();
        $currentStep = $data['current_step'];
        $getStep = $data['get_step'];
        $getNextStep = (!empty($data['get_next']) and $data['get_next'] == 1);
        $isDraft = (!empty($data['draft']) and $data['draft'] == 1);

        $upcomingCourse = UpcomingCourse::query()->where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('creator_id', $user->id)
                    ->orWhere('teacher_id', $user->id);
            })->first();

        if (empty($upcomingCourse)) {
            abort(404);
        }


        if ($currentStep == 1) {
            $rules = [
                'type' => 'required|in:webinar,course,text_lesson',
                'title' => 'required|max:255',
                'thumbnail' => 'required',
                'image_cover' => 'required',
                'description' => 'required',
            ];
        } else if ($currentStep == 2) {
            $rules = [
                'category_id' => 'required|exists:categories,id',
                'publish_date' => 'required',
                'capacity' => 'nullable|numeric',
                'price' => 'nullable|numeric',
                'duration' => 'nullable|numeric',
                'sections' => 'nullable|numeric',
                'parts' => 'nullable|numeric',
                'course_progress' => 'nullable|integer|between:0,100',
            ];
        }

        $this->validate($request, $rules);

        $upcomingCourseRulesRequired = false;
        if (($currentStep == 4 and !$getNextStep and !$isDraft) or (!$getNextStep and !$isDraft)) {
            $upcomingCourseRulesRequired = empty($data['rules']);
        }

        $data['status'] = ($isDraft or $upcomingCourseRulesRequired) ? UpcomingCourse::$isDraft : UpcomingCourse::$pending;

        if ($currentStep == 2) {
            $startDate = convertTimeToUTCzone($data['publish_date'], $data['timezone']);
            $data['publish_date'] = $startDate->getTimestamp();

            $data['support'] = (!empty($data['support']) and $data['support'] == "on");
            $data['certificate'] = (!empty($data['certificate']) and $data['certificate'] == "on");
            $data['include_quizzes'] = (!empty($data['include_quizzes']) and $data['include_quizzes'] == "on");
            $data['downloadable'] = (!empty($data['downloadable']) and $data['downloadable'] == "on");
            $data['forum'] = (!empty($data['forum']) and $data['forum'] == "on");
            $data['price'] = !empty($data['price']) ? convertPriceToDefaultCurrency($data['price']) : null;


            UpcomingCourseFilterOption::where('upcoming_course_id', $upcomingCourse->id)->delete();

            $filters = $request->get('filters', null);
            if (!empty($filters) and is_array($filters)) {
                foreach ($filters as $filter) {
                    UpcomingCourseFilterOption::create([
                        'upcoming_course_id' => $upcomingCourse->id,
                        'filter_option_id' => $filter
                    ]);
                }
            }

            Tag::where('upcoming_course_id', $upcomingCourse->id)->delete();
            if (!empty($request->get('tags'))) {
                $tags = explode(',', $request->get('tags'));

                foreach ($tags as $tag) {
                    Tag::create([
                        'upcoming_course_id' => $upcomingCourse->id,
                        'title' => $tag,
                    ]);
                }
            }
        } // .\ if $currentStep == 2

        if ($currentStep == 1) {
            $data = $this->handleVideoDemoData($request, $data, "upcoming_course_demo_" . time());

            UpcomingCourseTranslation::query()->updateOrCreate([
                'upcoming_course_id' => $upcomingCourse->id,
                'locale' => mb_strtolower($data['locale']),
            ], [
                'title' => $data['title'],
                'description' => $data['description'],
                'seo_description' => $data['seo_description'],
            ]);
        }

        unset($data['_token'],
            $data['current_step'],
            $data['draft'],
            $data['get_step'],
            $data['get_next'],
            $data['partners'],
            $data['tags'],
            $data['filters'],
            $data['ajax'],
            $data['title'],
            $data['description'],
            $data['seo_description'],
        );

        if (empty($data['teacher_id']) and $user->isOrganization() and $upcomingCourse->creator_id == $user->id) {
            $data['teacher_id'] = $user->id;
        }

        $upcomingCourse->update($data);

        $url = '/panel/upcoming_courses';
        if ($getNextStep) {
            $nextStep = (!empty($getStep) and $getStep > 0) ? $getStep : $currentStep + 1;

            $url = '/panel/upcoming_courses/' . $upcomingCourse->id . '/step/' . (($nextStep <= 4) ? $nextStep : 4);
        }

        if ($upcomingCourseRulesRequired) {
            $url = '/panel/upcoming_courses/' . $upcomingCourse->id . '/step/4';

            return redirect($url)->withErrors(['rules' => trans('validation.required', ['attribute' => 'rules'])]);
        }

        return redirect($url);
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize("panel_upcoming_courses_delete");

        $user = auth()->user();

        $upcomingCourse = UpcomingCourse::where('id', $id)
            ->where('creator_id', $user->id)
            ->first();

        if (!$upcomingCourse) {
            abort(404);
        }

        $upcomingCourse->delete();

        return response()->json([
            'code' => 200,
            'redirect_to' => $request->get('redirect_to')
        ], 200);
    }

    public function orderItems(Request $request)
    {
        $user = auth()->user();
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
                case 'faqs':
                    foreach ($itemIds as $order => $id) {
                        Faq::where('id', $id)
                            ->where('creator_id', $user->id)
                            ->update(['order' => ($order + 1)]);
                    }
                    break;

                case 'webinar_extra_descriptions_learning_materials':
                    foreach ($itemIds as $order => $id) {
                        WebinarExtraDescription::where('id', $id)
                            ->where('creator_id', $user->id)
                            ->where('type', 'learning_materials')
                            ->update(['order' => ($order + 1)]);
                    }
                    break;

                case 'webinar_extra_descriptions_company_logos':
                    foreach ($itemIds as $order => $id) {
                        WebinarExtraDescription::where('id', $id)
                            ->where('creator_id', $user->id)
                            ->where('type', 'company_logos')
                            ->update(['order' => ($order + 1)]);
                    }
                    break;

                case 'webinar_extra_descriptions_requirements':
                    foreach ($itemIds as $order => $id) {
                        WebinarExtraDescription::where('id', $id)
                            ->where('creator_id', $user->id)
                            ->where('type', 'requirements')
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

    public function followers($id)
    {
        $this->authorize("panel_upcoming_courses_followers");

        $user = auth()->user();

        $upcomingCourse = UpcomingCourse::query()
            ->where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('creator_id', $user->id)
                    ->orWhere('teacher_id', $user->id);
            })->first();

        if (!empty($upcomingCourse)) {

            $followers = UpcomingCourseFollower::query()->where('upcoming_course_id', $upcomingCourse->id)
                ->orderBy('created_at', 'asc')
                ->with([
                    'user' => function ($query) {
                        $query->select('id', 'full_name', 'avatar', 'avatar_settings', 'role_id', 'role_name');
                    }
                ])
                ->get();

            $data = [
                'pageTitle' => trans('update.course_followers'),
                'upcomingCourse' => $upcomingCourse,
                'followers' => $followers,
            ];

            return view('web.default.panel.upcoming_courses.followers', $data);
        }

        abort(404);
    }

    public function assignCourseModal($id)
    {
        $user = auth()->user();

        $upcomingCourse = UpcomingCourse::query()
            ->where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('creator_id', $user->id)
                    ->orWhere('teacher_id', $user->id);
            })->first();

        if (!empty($upcomingCourse)) {
            $webinars = Webinar::query()
                ->select('id', 'creator_id', 'teacher_id')
                ->where('status', 'active')
                ->where(function ($query) use ($user) {
                    $query->where('creator_id', $user->id)
                        ->orWhere('teacher_id', $user->id);
                })->get();

            $data = [
                'upcomingCourse' => $upcomingCourse,
                'webinars' => $webinars,
            ];

            $html = (string)view()->make('web.default.panel.upcoming_courses.assign_course_modal', $data);

            return response()->json([
                'code' => 200,
                'html' => $html
            ]);
        }

        abort(404);
    }

    public function storeAssignCourse(Request $request, $id)
    {
        $user = auth()->user();
        $data = $request->all();

        $validator = Validator::make($data, [
            'course' => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $webinar = Webinar::query()
            ->where('id', $data['course'])
            ->where('status', 'active')
            ->where(function ($query) use ($user) {
                $query->where('creator_id', $user->id)
                    ->orWhere('teacher_id', $user->id);
            })->first();

        if (empty($webinar)) {
            return response([
                'code' => 422,
                'errors' => [
                    'course' => [trans('validation.required', ['attribute' => 'course'])]
                ],
            ], 422);
        }

        $upcomingCourse = UpcomingCourse::query()
            ->where('id', $id)
            ->whereNull('webinar_id')
            ->where(function ($query) use ($user) {
                $query->where('creator_id', $user->id)
                    ->orWhere('teacher_id', $user->id);
            })->first();

        if (!empty($upcomingCourse)) {

            $upcomingCourse->update([
                'webinar_id' => $webinar->id
            ]);


            $notifyOptions = [
                '[item_title]' => $upcomingCourse->title,
            ];
            sendNotification("upcoming_course_published", $notifyOptions, $upcomingCourse->teacher_id);

            foreach ($upcomingCourse->followers as $follower) {
                sendNotification("upcoming_course_published_for_followers", $notifyOptions, $follower->user_id);
            }


            return response()->json([
                'code' => 200,
                'msg' => trans('update.assign_published_course_successful')
            ]);
        }

        abort(404);
    }

    public function followings()
    {
        $user = auth()->user();

        $upcomingIds = UpcomingCourseFollower::query()->where('user_id', $user->id)
            ->pluck('upcoming_course_id')
            ->toArray();

        $upcomingCourses = UpcomingCourse::query()
            ->whereIn('id', $upcomingIds)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.following_courses'),
            'upcomingCourses' => $upcomingCourses
        ];

        return view('web.default.panel.upcoming_courses.followings', $data);
    }
}
