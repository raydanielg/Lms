<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UpcomingCoursesExport;
use App\Http\Controllers\Admin\traits\ProductBadgeTrait;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Panel\Traits\VideoDemoTrait;
use App\Models\Category;
use App\Models\Role;
use App\Models\Tag;
use App\Models\Translation\UpcomingCourseTranslation;
use App\Models\UpcomingCourse;
use App\Models\UpcomingCourseFilterOption;
use App\Models\UpcomingCourseFollower;
use App\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UpcomingCoursesController extends Controller
{
    use ProductBadgeTrait, VideoDemoTrait;

    public function index(Request $request)
    {
        $this->authorize('admin_upcoming_courses_list');


        $query = UpcomingCourse::query();

        $totalCourses = deepClone($query)->count();
        $releasedCourses = deepClone($query)->whereNotNull('webinar_id')->count();
        $notReleased = deepClone($query)->whereNull('webinar_id')->count();
        $ids = deepClone($query)->pluck('id')->toArray();
        $followers = UpcomingCourseFollower::query()->whereIn('upcoming_course_id', $ids)->count();

        $upcomingCourses = $this->handleFilters($request, $query)
            ->withCount([
                'followers'
            ])
            ->with([
                'teacher' => function ($qu) {
                    $qu->select('id', 'full_name');
                }
            ])
            ->paginate(10);

        $categories = Category::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $data = [
            'pageTitle' => trans('update.upcoming_courses'),
            'totalCourses' => $totalCourses,
            'releasedCourses' => $releasedCourses,
            'notReleased' => $notReleased,
            'followers' => $followers,
            'upcomingCourses' => $upcomingCourses,
            'categories' => $categories
        ];

        return view('admin.upcoming_courses.lists', $data);
    }

    private function handleFilters(Request $request, $query)
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
            $query->where('status', $status);
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
                case 'only_not_released_courses':
                    $query->whereNull('webinar_id');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    public function create()
    {
        $this->authorize('admin_upcoming_courses_create');

        removeContentLocale();

        $teachers = User::where('role_name', Role::$teacher)->get();
        $categories = Category::where('parent_id', null)->get();

        $data = [
            'pageTitle' => trans('update.new_upcoming_course'),
            'teachers' => $teachers,
            'categories' => $categories
        ];

        return view('admin.upcoming_courses.create.index', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_upcoming_courses_create');

        $rules = [
            'type' => 'required|in:webinar,course,text_lesson',
            'title' => 'required|max:255',
            'thumbnail' => 'required',
            'image_cover' => 'required',
            'description' => 'required',
            'teacher_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'publish_date' => 'required',
            'timezone' => 'required',
            'capacity' => 'nullable|numeric',
            'price' => 'nullable|numeric',
            'duration' => 'nullable|numeric',
            'sections' => 'nullable|numeric',
            'parts' => 'nullable|numeric',
            'course_progress' => 'nullable|numeric',
        ];

        $this->validate($request, $rules);

        $data = $request->all();
        $storeData = $this->makeStoreData($request, $data);
        $storeData['status'] = UpcomingCourse::$pending;
        $storeData['created_at'] = time();

        $upcomingCourse = UpcomingCourse::query()->create($storeData);

        if (!empty($upcomingCourse)) {
            $this->storePublicItems($request, $upcomingCourse);

            return redirect(getAdminPanelUrl("/upcoming_courses/{$upcomingCourse->id}/edit"));
        }

        abort(500);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('admin_upcoming_courses_edit');

        $upcomingCourse = UpcomingCourse::query()->where('id', $id)
            ->with([
                'category' => function ($query) {
                    $query->with(['filters' => function ($query) {
                        $query->with('options');
                    }]);
                },
                'filterOptions',
                'tags',
                'faqs' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
                'extraDescriptions' => function ($query) {
                    $query->orderBy('order', 'asc');
                }
            ])->first();

        if (!empty($upcomingCourse)) {
            $teachers = User::where('role_name', Role::$teacher)->get();
            $categories = Category::where('parent_id', null)->get();

            $locale = $request->get('locale', app()->getLocale());
            storeContentLocale($locale, $upcomingCourse->getTable(), $upcomingCourse->id);

            $upcomingCourseTags = $upcomingCourse->tags->pluck('title')->toArray();

            $upcomingCourseCategoryFilters = !empty($upcomingCourse->category) ? $upcomingCourse->category->filters : [];

            if (empty($upcomingCourse->category) and !empty($request->old('category_id'))) {
                $category = Category::where('id', $request->old('category_id'))->first();

                if (!empty($category)) {
                    $upcomingCourseCategoryFilters = $category->filters;
                }
            }

            $definedLanguage = [];
            if ($upcomingCourse->translations) {
                $definedLanguage = $upcomingCourse->translations->pluck('locale')->toArray();
            }

            $data = [
                'pageTitle' => trans('public.edit') . ' | ' . $upcomingCourse->title,
                'teachers' => $teachers,
                'categories' => $categories,
                'upcomingCourse' => $upcomingCourse,
                'upcomingCourseTags' => $upcomingCourseTags,
                'upcomingCourseCategoryFilters' => $upcomingCourseCategoryFilters,
                'definedLanguage' => $definedLanguage,
            ];

            return view('admin.upcoming_courses.create.index', $data);
        }

        abort(404);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_upcoming_courses_edit');

        $upcomingCourse = UpcomingCourse::query()->where('id', $id)->first();

        if (!empty($upcomingCourse)) {
            $rules = [
                'type' => 'required|in:webinar,course,text_lesson',
                'title' => 'required|max:255',
                'slug' => 'max:255|unique:upcoming_courses,slug,' . $upcomingCourse->id,
                'thumbnail' => 'required',
                'image_cover' => 'required',
                'description' => 'required',
                'teacher_id' => 'required|exists:users,id',
                'category_id' => 'required|exists:categories,id',
                'publish_date' => 'required',
                'timezone' => 'required',
                'capacity' => 'nullable|numeric',
                'price' => 'nullable|numeric',
                'duration' => 'nullable|numeric',
                'sections' => 'nullable|numeric',
                'parts' => 'nullable|numeric',
                'course_progress' => 'nullable|numeric',
            ];

            $this->validate($request, $rules);

            $data = $request->all();
            $isDraft = (!empty($data['draft']) and $data['draft'] == 1);
            $reject = (!empty($data['draft']) and $data['draft'] == 'reject');
            $publish = (!empty($data['draft']) and $data['draft'] == 'publish');

            // Product Badge
            $this->handleProductBadges($upcomingCourse, $data);

            $storeData = $this->makeStoreData($request, $data);
            $storeData['status'] = $publish ? UpcomingCourse::$active : ($reject ? UpcomingCourse::$inactive : ($isDraft ? UpcomingCourse::$isDraft : UpcomingCourse::$pending));

            $upcomingCourse->update($storeData);

            $this->storePublicItems($request, $upcomingCourse);


            return redirect(getAdminPanelUrl("/upcoming_courses/{$upcomingCourse->id}/edit"));
        }

        abort(404);
    }

    private function makeStoreData(Request $request, $data): array
    {
        $startDate = convertTimeToUTCzone($data['publish_date'], $data['timezone']);
        $data['price'] = !empty($data['price']) ? convertPriceToDefaultCurrency($data['price']) : null;

        $data = $this->handleVideoDemoData($request, $data, "upcoming_course_demo_" . time());

        return [
            'creator_id' => $data['teacher_id'],
            'teacher_id' => $data['teacher_id'],
            'category_id' => $data['category_id'],
            'slug' => !empty($data['slug']) ? $data['slug'] : UpcomingCourse::makeSlug($data['title']),
            'type' => $data['type'],
            'thumbnail' => $data['thumbnail'],
            'image_cover' => $data['image_cover'],
            'video_demo' => $data['video_demo'] ?? null,
            'video_demo_source' => $data['video_demo'] ? $data['video_demo_source'] : null,
            'publish_date' => $startDate->getTimestamp(),
            'timezone' => $data['timezone'],
            'capacity' => $data['capacity'] ?? null,
            'price' => $data['price'] ?? null,
            'duration' => $data['duration'] ?? null,
            'sections' => $data['sections'] ?? null,
            'parts' => $data['parts'] ?? null,
            'course_progress' => $data['course_progress'] ?? null,
            'support' => (!empty($data['support']) and $data['support'] == "on"),
            'certificate' => (!empty($data['certificate']) and $data['certificate'] == "on"),
            'include_quizzes' => (!empty($data['include_quizzes']) and $data['include_quizzes'] == "on"),
            'downloadable' => (!empty($data['downloadable']) and $data['downloadable'] == "on"),
            'forum' => (!empty($data['forum']) and $data['forum'] == "on"),
            'message_for_reviewer' => !empty($data['message_for_reviewer']) ? $data['message_for_reviewer'] : null,
        ];
    }

    private function storePublicItems(Request $request, $upcomingCourse)
    {
        $data = $request->all();

        UpcomingCourseTranslation::query()->updateOrCreate([
            'upcoming_course_id' => $upcomingCourse->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
            'description' => $data['description'],
            'seo_description' => $data['seo_description'],
        ]);

        UpcomingCourseFilterOption::where('upcoming_course_id', $upcomingCourse->id)->delete();
        Tag::where('upcoming_course_id', $upcomingCourse->id)->delete();


        $filters = $request->get('filters', null);
        if (!empty($filters) and is_array($filters)) {
            foreach ($filters as $filter) {
                UpcomingCourseFilterOption::create([
                    'upcoming_course_id' => $upcomingCourse->id,
                    'filter_option_id' => $filter
                ]);
            }
        }

        if (!empty($request->get('tags'))) {
            $tags = explode(',', $request->get('tags'));

            foreach ($tags as $tag) {
                Tag::create([
                    'upcoming_course_id' => $upcomingCourse->id,
                    'title' => $tag,
                ]);
            }
        }
    }

    public function destroy($id)
    {
        $this->authorize('admin_upcoming_courses_delete');

        $upcomingCourse = UpcomingCourse::query()->findOrFail($id);

        $upcomingCourse->delete();

        return redirect(getAdminPanelUrl('/upcoming_courses'));
    }

    public function approve(Request $request, $id)
    {
        $this->authorize('admin_upcoming_courses_delete');

        $upcomingCourse = UpcomingCourse::query()->findOrFail($id);

        $upcomingCourse->update([
            'status' => UpcomingCourse::$active
        ]);

        $notifyOptions = [
            '[item_title]' => $upcomingCourse->title,
        ];
        sendNotification("upcoming_course_approved", $notifyOptions, $upcomingCourse->teacher_id);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.course_status_changes_to_approved'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl('/upcoming_courses'))->with(['toast' => $toastData]);
    }

    public function reject(Request $request, $id)
    {
        $this->authorize('admin_upcoming_courses_delete');

        $upcomingCourse = UpcomingCourse::query()->findOrFail($id);

        $upcomingCourse->update([
            'status' => UpcomingCourse::$inactive
        ]);

        $notifyOptions = [
            '[item_title]' => $upcomingCourse->title,
        ];
        sendNotification("upcoming_course_rejected", $notifyOptions, $upcomingCourse->teacher_id);


        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.course_status_changes_to_rejected'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl('/upcoming_courses'))->with(['toast' => $toastData]);
    }

    public function unpublish(Request $request, $id)
    {
        $this->authorize('admin_upcoming_courses_delete');

        $upcomingCourse = UpcomingCourse::query()->findOrFail($id);

        $upcomingCourse->update([
            'status' => UpcomingCourse::$pending
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.course_status_changes_to_unpublished'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl('/upcoming_courses'))->with(['toast' => $toastData]);
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('admin_upcoming_courses_export_excel');

        $query = UpcomingCourse::query();

        $query = $this->handleFilters($request, $query)
            ->with(['teacher' => function ($qu) {
                $qu->select('id', 'full_name');
            }])
            ->withCount([
                'followers'
            ]);

        $items = $query->get();

        $export = new UpcomingCoursesExport($items);

        return Excel::download($export, 'upcoming_courses.xlsx');
    }

    public function followers(Request $request, $id)
    {
        $this->authorize('admin_upcoming_courses_followers');

        $upcomingCourse = UpcomingCourse::query()->findOrFail($id);

        $query = UpcomingCourseFollower::query()
            ->where('upcoming_course_id', $upcomingCourse->id);

        $totalFollowers = deepClone($query)->count();

        $followers = $this->handleFollowersFilters($request, $query)
            ->with([
                'user'
            ])
            ->paginate(10);

        $roles = Role::all();

        $data = [
            'pageTitle' => $upcomingCourse->title . ' - ' . trans('update.followers'),
            'upcomingCourse' => $upcomingCourse,
            'followers' => $followers,
            'totalFollowers' => $totalFollowers,
            'roles' => $roles,
        ];

        return view('admin.upcoming_courses.followers', $data);
    }

    private function handleFollowersFilters(Request $request, $query)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $full_name = $request->get('full_name');
        $role_id = $request->get('role_id');

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($full_name)) {
            $query->whereHas('user', function ($query) use ($full_name) {
                $query->where('full_name', 'like', "%$full_name%");
            });
        }

        if (!empty($role_id)) {
            $query->whereHas('user', function ($query) use ($role_id) {
                $query->where('role_id', $role_id);
            });
        }

        return $query;
    }

    public function deleteFollow($upcomingId, $followId)
    {
        $this->authorize('admin_upcoming_courses_followers');

        $follow = UpcomingCourseFollower::query()
            ->where('upcoming_course_id', $upcomingId)
            ->where('id', $followId)
            ->first();

        if (!empty($follow)) {
            $follow->delete();

            $toastData = [
                'title' => trans('public.request_success'),
                'msg' => trans('update.items_deleted_successful'),
                'status' => 'success'
            ];

            return back()->with(['toast' => $toastData]);
        }

        abort(404);
    }

    public function search(Request $request)
    {
        $term = $request->get('term');

        $upcomingCourse = UpcomingCourse::select('id')
            ->whereTranslationLike('title', "%$term%")
            ->get();

        return response()->json($upcomingCourse, 200);
    }
}
