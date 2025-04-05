<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\FileResource;
use App\Http\Resources\SessionResource;
use App\Http\Resources\TextLessonResource;
use App\Http\Resources\WebinarChapterResource;
use App\Mixins\Cashback\CashbackRules;
use App\Models\Favorite;
use App\Models\CourseLearning;
use App\Models\Ticket;
use App\Models\Api\Webinar;
use App\Models\WebinarChapter;
use App\Models\WebinarFilterOption;
use App\Models\WebinarReport;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebinarController extends Controller
{


    public function index()
    {
        $webinars = Webinar::where('webinars.status', 'active')
            ->with([
                "badges" => function ($query) {
                    $query->where('targetable_type', 'App\Models\Webinar');
                    $query->with([
                        'badge' => function ($query) {
                            $time = time();
                            $query->where('enable', true);

                            $query->where(function ($query) use ($time) {
                                $query->whereNull('start_at');
                                $query->orWhere('start_at', '<', $time);
                            });

                            $query->where(function ($query) use ($time) {
                                $query->whereNull('end_at');
                                $query->orWhere('end_at', '>', $time);
                            });
                        }
                    ]);
                },
            ])
            ->whereHas('teacher', function ($query) {
                $query->where('status', 'active')
                    ->where(function ($query) {
                        $query->where('ban', false)
                            ->orWhere(function ($query) {
                                $query->whereNotNull('ban_end_at')
                                    ->where('ban_end_at', '<', time());
                            });
                    });
            })
            ->where('private', false)
            ->handleFilters()
            ->get()->map(function ($webinar) {
                return $webinar->brief;
            });


        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $webinars);

    }

    public function show($id)
    {
        $user = apiAuth();

        $webinar = Webinar::where('status', 'active')
            ->where('private', false)
            ->where('id', $id)
            ->first();

        if (!empty($webinar)) {
            $data = $webinar->details;
            $cashbackRules = null;

            if (!empty($data["price"]) and getFeaturesSettings('cashback_active') and (empty($user) or !$user->disable_cashback)) {
                $cashbackRulesMixin = new CashbackRules($user);
                $cashbackRules = $cashbackRulesMixin->getRules('courses', $data["id"], $data["type"], null, null);
            }

            $data["cashbackRules"] = $cashbackRules;

            return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $data);
        }

        return apiResponse2(0, 'invalid', trans('api.public.invalid'));
    }

    public function content($id)
    {
        $user = apiAuth();
        $webinar = Webinar::where('id', $id)
            ->with([
                'chapters' => function ($query) use ($user) {
                    $query->where('status', WebinarChapter::$chapterActive);
                    $query->orderBy('order', 'asc');

                    $query->with([
                        'chapterItems' => function ($query) {
                            $query->orderBy('order', 'asc');
                        }
                    ]);
                },
                'quizzes' => function ($query) {
                    $query->where('status', 'active')
                        ->with(['quizResults', 'quizQuestions']);
                },
                'files' => function ($query) use ($user) {
                    $query->join('webinar_chapters', 'webinar_chapters.id', '=', 'files.chapter_id')
                        ->select('files.*', DB::raw('webinar_chapters.order as chapterOrder'))
                        ->where('files.status', WebinarChapter::$chapterActive)
                        ->orderBy('chapterOrder', 'asc')
                        ->orderBy('files.order', 'asc')
                        ->with([
                            'learningStatus' => function ($query) use ($user) {
                                $query->where('user_id', !empty($user) ? $user->id : null);
                            }
                        ]);
                },
                'textLessons' => function ($query) use ($user) {
                    $query->where('status', WebinarChapter::$chapterActive)
                        ->withCount(['attachments'])
                        ->orderBy('order', 'asc')
                        ->with([
                            'learningStatus' => function ($query) use ($user) {
                                $query->where('user_id', !empty($user) ? $user->id : null);
                            }
                        ]);
                },
                'sessions' => function ($query) use ($user) {
                    $query->where('status', WebinarChapter::$chapterActive)
                        ->orderBy('order', 'asc')
                        ->with([
                            'learningStatus' => function ($query) use ($user) {
                                $query->where('user_id', !empty($user) ? $user->id : null);
                            }
                        ]);
                },
                'assignments' => function ($query) {
                    $query->where('status', WebinarChapter::$chapterActive);
                },
            ])
            ->first();

        if (!empty($webinar)) {
            $chapters = collect(WebinarChapterResource::collection($webinar->chapters))->map(function (WebinarChapterResource $item) {
                return array_merge(['type' => 'chapter'], $item->toArray(null));
            });

            $files = collect(FileResource::collection($webinar->files->whereNull('chapter_id')))->map(function (FileResource $item) {
                return array_merge(['type' => 'file'], $item->toArray(null));
            });
            $sessions = collect(SessionResource::collection($webinar->sessions->whereNull('chapter_id')))->map(function (SessionResource $item) {
                return array_merge(['type' => 'session'], $item->toArray(null));
            });
            $textLessons = collect(TextLessonResource::collection($webinar->textLessons->whereNull('chapter_id')))->map(function (TextLessonResource $item) {
                return array_merge(['type' => 'text_lesson'], $item->toArray(null));
            });

            $content = $chapters->merge($files)->merge($sessions)->merge($textLessons);
            return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $content);
        }

        return apiResponse2(0, 'invalid', trans('api.public.invalid'));
    }

    public function learningStatus(Request $request, $webinar_id)
    {
        switch ($request->input('item')) {
            case 'file_id':
                $table = 'files';
                break;

            case 'session_id':
                $table = 'sessions';
                break;

            case 'text_lesson_id':
                $table = 'text_lessons';
                break;
            default :
                $table = null;

        }

        validateParam($request->all(), [
            'item' => 'required|in:file_id,session_id,text_lesson_id',
            'item_id' => ['required', Rule::exists($table, 'id')],
            'status' => 'required|boolean',
        ]);

        $user = apiAuth();
        $data = $request->all();

        $item = $data['item'];
        $item_id = $data['item_id'];
        $status = $data['status'];

        $course = Webinar::where('id', $webinar_id)->first();

        if (empty($course)) {
            abort(404);
        }


        if (!$course->checkUserHasBought($user)) {

            return apiResponse2(0, 'not_purchased', trans('api.webinar.not_purchased'));
        }


        $courseLearning = CourseLearning::where('user_id', $user->id)
            ->where($item, $item_id)->delete();


        if ($status) {

            CourseLearning::create([
                'user_id' => $user->id,
                $item => $item_id,
                'created_at' => time()
            ]);

            return apiResponse2(1, 'read', trans('api.learning_status.read'));

        }
        return apiResponse2(1, 'unread', trans('api.learning_status.unread'));


    }

    public function report(Request $request, $id)
    {
        $user = apiAuth();
        validateParam($request->all(), [
            'reason' => 'required|string',
            'message' => 'required|string',
        ]);

        $webinar = Webinar::select('id', 'status')
            ->where('id', $id)
            ->where('status', 'active')
            ->first();
        if (!$webinar) {
            abort(404);
        }

        WebinarReport::create([
            'user_id' => $user->id,
            'webinar_id' => $webinar->id,
            'reason' => $request->post('reason'),
            'message' => $request->post('message'),
            'created_at' => time()
        ]);
        return apiResponse2(1, 'reported', trans('courses.reported'));
    }

    public static function brief($webinars, $single = false)
    {
        if ($single) {
            $webinars = collect([$webinars]);
        }
        //
        $user = apiAuth();
        $webinars = $webinars->map(function ($webinar) use ($user) {

            $hasBought = $webinar->checkUserHasBought($user);

            /* progressbar status */
            $progress = self::progress($webinar);

            /* is user favorite */
            $is_favorite = self::isFavorite($webinar);

            /* live webinar status */
            $live_webinar_status = self::liveWebinarStatus($webinar);

            return [
                'auth' => ($user) ? true : false,
                'id' => $webinar->id,
                'status' => $webinar->status,
                'title' => $webinar->title,
                'type' => $webinar->type,
                'live_webinar_status' => $live_webinar_status,
                'auth_has_bought' => $hasBought,

                'price' => $webinar->price,
                'price_with_discount' => ($webinar->activeSpecialOffer()) ? (
                number_format($webinar->price - ($webinar->price * $webinar->activeSpecialOffer()->percent / 100), 2)) : false,
                'active_special_offer' => $webinar->activeSpecialOffer(),

                'duration' => $webinar->duration,
                'teacher' => [
                    'full_name' => $webinar->teacher->full_name,
                    'avatar' => $webinar->teacher->getAvatar(),
                    'rate' => $webinar->teacher->rates(),
                ],
                'rate' => $webinar->getRate(),
                'discount' => $webinar->getDiscount(),
                'created_at' => $webinar->created_at,
                'start_date' => $webinar->start_date,
                'progress' => $webinar->getProgress(),
                'category' => $webinar->category->title,

            ];
        });

        if ($single) {
            return $webinars->first();
        }

        return [
            'count' => count($webinars),
            'webinars' => $webinars,
        ];
    }

    public function details($webinars)
    {
        $user = apiAuth();

        $webinars = $webinars->map(function ($webinar) use ($user) {
            $hasBought = $webinar->checkUserHasBought($user);

            /* progressbar status */
            $progress = $this->progress($webinar);

            /* is user favorite */
            $is_favorite = $this->isFavorite($webinar);

            /* live webinar status */
            $live_webinar_status = $this->liveWebinarStatus($webinar);

            return [
                'auth' => ($user) ? true : false,
                'id' => $webinar->id,
                'title' => $webinar->title,
                'type' => $webinar->type,
                'live_webinar_status' => $live_webinar_status,
                'auth_has_bought' => $hasBought,
                'price' => $webinar->price,
                'price_with_discount' => ($webinar->activeSpecialOffer()) ? (
                number_format($webinar->price - ($webinar->price * $webinar->activeSpecialOffer()->percent / 100), 2)) : false,
                'active_special_offer' => $webinar->activeSpecialOffer(),

                'duration' => $webinar->duration,
                'teacher' => [
                    'full_name' => $webinar->teacher->full_name,
                    'avatar' => $webinar->teacher->getAvatar(),
                    'rate' => $webinar->teacher->rates(),
                ],

                'sessions_count' => $webinar->sessions->count(),
                'text_lessons_count' => $webinar->textLessons->count(),
                'files_count' => $webinar->files->count(),
                /*    $sessionChapters = $course->chapters->where('type', WebinarChapter::$chapterSession);
                $sessionsWithoutChapter = $course->sessions->whereNull('chapter_id');*/

                'sessions_without_chapter' => $webinar->sessions->whereNull('chapter_id')->map(function ($session) {
                    return [
                        'id' => $session->id,
                        'title' => $session->title,
                        'description' => $session->description,
                        'date' => dateTimeFormat($session->date, 'j M Y | H:i')
                    ];

                }),
                'sessions_with_chapter' => $webinar->chapters->where('type', WebinarChapter::$chapterSession)->map(function ($chapter) {
                    $chapter->sessions->map(function ($session) {
                        return [
                            'id' => $session->id,
                            'title' => $session->title,
                            'description' => $session->description,
                            'date' => dateTimeFormat($session->date, 'j M Y | H:i')
                        ];
                    });


                }),

                'rate' => $webinar->getRate(),
                'rate_type' => [
                    'content_quality' => $webinar->reviews->count() > 0 ? round($webinar->reviews->avg('content_quality'), 1) : 0,
                    'instructor_skills' => $webinar->reviews->count() > 0 ? round($webinar->reviews->avg('instructor_skills'), 1) : 0,
                    'purchase_worth' => $webinar->reviews->count() > 0 ? round($webinar->reviews->avg('purchase_worth'), 1) : 0,
                    'support_quality' => $webinar->reviews->count() > 0 ? round($webinar->reviews->avg('support_quality'), 1) : 0,

                ],
                'reviews_count' => $webinar->reviews->count(),
                'reviews' => $webinar->reviews->map(function ($review) {
                    return [
                        'user' => [
                            'full_name' => $review->creator->full_name,
                            'avatar' => $review->creator->getAvatar(),
                        ],
                        'create_at' => $review->created_at,
                        'description' => $review->description,
                        'replies' => $review->comments->map(function ($reply) {
                            return [
                                'user' => [
                                    'full_name' => $reply->user->full_name,
                                    'avatar' => $reply->user->getAvatar(),
                                ],
                                'create_at' => $reply->created_at,
                                'comment' => $reply->comment,
                            ];

                        })


                    ];
                }),
                'comments' => $webinar->comments->map(function ($item) {
                    return [
                        'user' => [
                            'full_name' => $item->user->full_name,
                            'avatar' => $item->user->getAvatar(),
                        ],
                        'create_at' => $item->created_at,
                        'comment' => $item->comment,
                        'replies' => $item->replies->map(function ($reply) {
                            return [
                                'user' => [
                                    'full_name' => $reply->user->full_name,
                                    'avatar' => $reply->user->getAvatar(),
                                ],
                                'create_at' => $reply->created_at,
                                'comment' => $reply->comment,
                            ];

                        })
                    ];
                }),
                'discount' => $webinar->getDiscount(),
                'created_at' => $webinar->created_at,
                'start_date' => $webinar->start_date,

                'progress' => $progress,
                //'progressa' => $webinar->$progress,
                'category' => $webinar->category->title,
                'video_demo' => $webinar->video_demo,
                'image' => $webinar->getImage(),
                'description' => $webinar->description,
                'isDownloadable' => $webinar->isDownloadable(),
                'support' => $webinar->support ? true : false,
                'certificate' => ($webinar->quizzes->where('certificate', 1)->count() > 0) ? true : false,
                'quizzes_count' => $webinar->quizzes->where('status', \App\models\Quiz::ACTIVE)->count(),
                'is_favorite' => $is_favorite,
                'students_count' => $webinar->sales->count(),
                'tags' => $webinar->tags,
                'tickets' => $webinar->tickets->map(function ($ticket) {
                    return [
                        'id' => $ticket->id,
                        'title' => $ticket->title,
                        'sub_title' => $ticket->getSubTitle(),
                        'discount' => $ticket->discount,
                        //  'order' => $ticket->order,
                        'is_valid' => $ticket->isValid(),

                    ];
                }),
                'prerequisites' => $webinar->prerequisites->map(function ($prerequisite) {
                    return [
                        'required' => $prerequisite->required,
                        'webinar' => self::brief($prerequisite->prerequisiteWebinar, true)
                    ];
                }),
                'faqs' => $webinar->faqs

            ];
        });
        return [
            'count' => count($webinars),
            'webinars' => $webinars,
        ];
    }

    public static function getSingle($id)
    {
        $webinar = Webinar::where('status', 'active')
            ->where('private', false)->where('id', $id)->first();
        //  dd($webinar->id);
        if (!$webinar) {
            return null;
        }
        return self::brief($webinar, true);

    }

    public function handleFilters($request, $query)
    {
        $offset = $request->get('offset', null);
        $limit = $request->get('limit', null);
        $upcoming = $request->get('upcoming', null);
        $isFree = $request->get('free', null);
        $withDiscount = $request->get('discount', null);
        $isDownloadable = $request->get('downloadable', null);
        $sort = $request->get('sort', null);
        $filterOptions = $request->get('filter_option', null);
        $typeOptions = $request->get('type', []);
        $moreOptions = $request->get('moreOptions', []);
        $category = $request->get('cat', null);

        if (!empty($category) and is_numeric($category)) {
            $query->where('category_id', $category);
        }
        if (!empty($upcoming) and $upcoming == 1) {
            $query->whereNotNull('start_date')
                ->where('start_date', '>=', time());
        }

        if (!empty($isFree) and $isFree == 1) {
            $query->where(function ($qu) {
                $qu->whereNull('price')
                    ->orWhere('price', '0');
            });
        }

        if (!empty($isDownloadable) and $isDownloadable == 1) {
            $query->where('downloadable', 1);
        }

        if (!empty($withDiscount) and $withDiscount == 1) {
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

            $webinarIdsHasDiscount = array_unique($webinarIdsHasDiscount);

            $query->whereIn('webinars.id', $webinarIdsHasDiscount);
        }

        if (!empty($sort)) {
            if ($sort == 'expensive') {
                $query->orderBy('price', 'desc');
            }

            if ($sort == 'newest') {
                $query->orderBy('created_at', 'desc');
            }

            if ($sort == 'inexpensive') {
                $query->orderBy('price', 'asc');
            }

            if ($sort == 'bestsellers') {
                $query->whereHas('sales')
                    ->with('sales')
                    ->get()
                    ->sortBy(function ($qu) {
                        return $qu->sales->count();
                    });
            }

            if ($sort == 'best_rates') {
                $query->whereHas('reviews', function ($query) {
                    $query->where('status', 'active');
                })->with('reviews')
                    ->get()
                    ->sortBy(function ($qu) {
                        return $qu->reviews->avg('rates');
                    });
            }
        }

        if (!empty($filterOptions)) {
            $webinarIdsFilterOptions = WebinarFilterOption::where('filter_option_id', $filterOptions)
                ->pluck('webinar_id')
                ->toArray();

            $query->whereIn('webinars.id', $webinarIdsFilterOptions);
        }

        if (!empty($typeOptions) and is_array($typeOptions)) {
            $query->whereIn('type', $typeOptions);
        }

        if (!empty($moreOptions) and is_array($moreOptions)) {
            if (in_array('subscribe', $moreOptions)) {
                $query->where('subscribe', 1);
            }

            if (in_array('certificate_included', $moreOptions)) {
                $query->whereHas('quizzes', function ($query) {
                    $query->where('certificate', 1)
                        ->where('status', 'active');
                });
            }

            if (in_array('with_quiz', $moreOptions)) {
                $query->whereHas('quizzes', function ($query) {
                    $query->where('status', 'active');
                });
            }

            if (in_array('featured', $moreOptions)) {
                $query->whereHas('feature', function ($query) {
                    $query->whereIn('page', ['home_categories', 'categories'])
                        ->where('status', 'publish');
                });
            }
        }

        if (!empty($offset) && !empty($limit)) {
            $query->skip($offset);
        }
        if (!empty($limit)) {
            $query->take($limit);
        }
        return $query;
    }

    private static function liveWebinarStatus($webinar)
    {
        $live_webinar_status = false;
        if ($webinar->type == 'webinar') {
            if ($webinar->start_date > time()) {
                $live_webinar_status = 'not_conducted';
            } elseif ($webinar->isProgressing()) {
                $live_webinar_status = 'in_progress';
            } else {
                $live_webinar_status = 'finished';
            }
        }
        return $live_webinar_status;


    }

    private static function progress($webinar)
    {
        $user = apiAuth();
        /* progressbar status */
        $hasBought = $webinar->checkUserHasBought($user);
        $progress = null;
        if ($hasBought or $webinar->isWebinar()) {
            if ($webinar->isWebinar()) {
                if ($hasBought and $webinar->isProgressing()) {
                    $progress = $webinar->getProgress();
                } else {
                    $progress = $webinar->sales()->count() . '/' . $webinar->capacity;
                }
            } else {
                $progress = $webinar->getProgress();
            }
        }

        return $progress;
    }

    private static function isFavorite($webinar)
    {
        $user = apiAuth();
        $isFavorite = false;
        if (!empty($user)) {
            $isFavorite = Favorite::where('webinar_id', $webinar->id)
                ->where('user_id', $user->id)
                ->first();
        }
        return ($isFavorite) ? true : false;
    }

}
