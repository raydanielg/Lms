<?php

namespace App\Models\Api;

use App\Models\Api\Traits\CheckForSaleTrait;
use App\Models\Api\Traits\WebinarChartTrait;
use App\Models\CourseForum;
use App\Models\SpecialOffer;
use App\Models\Webinar as Model;
use App\Models\Sale;
use App\Models\Ticket;
use App\Models\WebinarChapterItem;
use App\Models\WebinarFilterOption;
use App\Models\CourseLearning;
use Illuminate\Support\Facades\DB;

class Webinar extends Model
{
    use WebinarChartTrait;
    use CheckForSaleTrait;

    public function can_view_error()
    {
        $error = null;

        $user = apiAuth();
        if ($user) {

            if (!$user->access_content) {
                $error = [trans('update.not_access_to_content'), trans('update.not_access_to_content_hint')];
            }
            //   return $user->private_content ? null : 'private_content';
        } else {

            if (getFeaturesSettings('webinar_private_content_status')) {
                $error = [trans('update.private_content'), trans('update.private_content_login_hint')];

            }
            // return !getFeaturesSettings('webinar_private_content_status');
        }
        return $error;
    }

    public function getBriefAttribute()
    {
        if (!$this) {
            return null;
        }

        $user = apiAuth();
        $hasBought = $this->checkUserHasBought($user);
        //  $sale = Sale::where('buyer_id', $user->id)->where('webinar_id', $this->id)->first();

        $rate = $this->getRate();

        return [
            'image' => url($this->getImage()),
            'auth' => ($user) ? true : false,
            'can' => [
                'view' => !$this->can_view_error(),
            ],
            'can_view_error' => $this->can_view_error(),

            'id' => $this->id,
            'status' => $this->status,
            'label' => $this->label,
            'title' => $this->title,
            'type' => $this->type,
            'link' => $this->getUrl(), // getExpiredAccessDays
            'access_days' => $this->access_days,
            //  'expired' => ($sale and $this->access_days and !$this->checkHasExpiredAccessDays($sale->created_at)),
            //   'expire_on' => ($sale and $this->getExpiredAccessDays($sale->created_at) ) ? $this->getExpiredAccessDays($sale->created_at) : null,
            //  'expired' => ($sale and $this->checkHasExpiredAccessDays($sale->created_at)) ?$this->getExpiredAccessDays($sale->created_at)  : false,
            'live_webinar_status' => $this->liveWebinarStatus(),
            'auth_has_bought' => $hasBought,
            'sales' => [
                'count' => $this->sales->count(),
                'amount' => $this->sales->sum('amount'),
            ],
            'sales_count_number'=>$this->sales_count_number,
            'is_favorite' => $this->isFavorite(),

            'price_string' => ($this->price > 0) ? handlePrice($this->price) : null,
            'best_ticket_string' => ($this->price > 0 and $this->bestTicket() < $this->price) ? handlePrice($this->bestTicket()) : null,

            'price' => nicePriceWithTax($this->price)['price'],
            'tax' => nicePriceWithTax($this->price)['tax'],
            'tax_with_discount' => nicePriceWithTax($this->bestTicket(true)['bestTicket'])['tax'],
            'best_ticket_price' => round(nicePriceWithTax($this->bestTicket(true)['bestTicket'])['price'], 3),
            'discount_percent' => $this->bestTicket(true)['percent'],


            'course_page_tax' => (!$this->activeSpecialOffer()) ? nicePriceWithTax($this->price)['tax'] :
                nicePriceWithTax(number_format($this->price - ($this->price * $this->activeSpecialOffer()->percent / 100), 2))['tax']
            ,
            'price_with_discount' => nicePrice(($this->activeSpecialOffer()) ? (
            number_format($this->price - ($this->price * $this->activeSpecialOffer()->percent / 100), 2))
                : $this->price),

            'discount_amount' =>
                ((int)nicePriceWithTax($this->price)['price']
                    - (int)round(nicePriceWithTax($this->bestTicket(true)['bestTicket'])['price']
                    )),


            'active_special_offer' => $this->activeSpecialOffer() ?: null,

            // 'discount' => $this->getDiscount(),

            'duration' => $this->duration,
            'teacher' => $this->teacher->brief,
            'students_count' => $this->sales->count(),
            'rate' => ($rate > 0) ? (float)$rate : 0,
            'rate_type' => [
                'content_quality' => $this->reviews->count() > 0 ? round($this->reviews->avg('content_quality'), 1) : 0,
                'instructor_skills' => $this->reviews->count() > 0 ? round($this->reviews->avg('instructor_skills'), 1) : 0,
                'purchase_worth' => $this->reviews->count() > 0 ? round($this->reviews->avg('purchase_worth'), 1) : 0,
                'support_quality' => $this->reviews->count() > 0 ? round($this->reviews->avg('support_quality'), 1) : 0,

            ],
            'created_at' => $this->created_at,
            'start_date' => $this->start_date,
            'purchased_at' => $this->purchasedDate(),
            'reviews_count' => $this->reviews->pluck('creator_id')->count(),
            'points' => $this->points,

            'progress' => $this->progress(),
            'progress_percent' => $this->getProgress(),
            'category' => $this->category->title ?? null,
            'capacity' => $this->capacity,
            'isPrivate' => $this->private,
            'forum' => getFeaturesSettings("course_forum_status") == "1" ? $this->forum : 0,
            'badges' => $this->badges,

        ];
    }

    public function getDiscount($ticket = null, $user = null)
    {
        $activeSpecialOffer = $this->activeSpecialOffer();

        $discountOut = $activeSpecialOffer ? $this->price * $activeSpecialOffer->percent / 100 : 0;

        if (!empty($user) and !empty($user->getUserGroup()) and isset($user->getUserGroup()->discount) and $user->getUserGroup()->discount > 0) {
            $discountOut += $this->price * $user->getUserGroup()->discount / 100;
        }

        if (!empty($ticket)) {
            $discountOut += $this->price * $ticket->discount / 100;
        }

        return $discountOut;
    }

    public function getDetailsAttribute()
    {
        $user = apiAuth();
        $hasBought = $this->checkUserHasBought($user);
        $details = [
            'support' => $this->support ? true : false,
            'subscribe' => $this->subscribe ? true : false,
            'description' => $this->description,
            'prerequisites' => $this->prerequisites()
                ->whereHas('prerequisiteWebinar')
                ->orderBy('order', 'asc')
                ->get()
                ->map(function ($prerequisite) {
                    if ($prerequisite->prerequisiteWebinar) {
                        return [
                            'required' => $prerequisite->required,
                            'webinar' => $prerequisite->prerequisiteWebinar->brief ?? null,
                        ];
                    }
                }),
            'faqs' => $this->faqs()->orderBy('order', 'asc')
                ->get()
                ->map(function ($faq) {
                    return $faq->details;
                }),

            'comments' => $this->comments()
                ->where('status', 'active')
                ->whereNull('reply_id')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($comment) {
                    return $comment->details;

                }),

            'session_chapters' => $this->chapters()
                ->whereHas('chapterItems', function ($query) {
                    $query->where('type', WebinarChapterItem::$chapterSession);
                })
                ->where('status', WebinarChapter::$chapterActive)
                ->orderBy('order', 'asc')
                ->get()
                ->map(function ($chapter) {
                    return $chapter->details;
                }),

            'sessions_without_chapter' => $this->sessions()
                ->where('status', WebinarChapter::$chapterActive)
                ->orderBy('order', 'asc')
                ->whereNull('chapter_id')
                ->get()->map(function ($session) {
                    return $session->details;
                }),
            'sessions_count' => $this->sessions()
                ->where('status', WebinarChapter::$chapterActive)
                ->count(),

            'files_chapters' => $this->chapters()
                ->whereHas('chapterItems', function ($query) {
                    $query->where('type', WebinarChapterItem::$chapterFile);
                })
                ->where('status', WebinarChapter::$chapterActive)
                //     ->where('type', WebinarChapter::$chapterFile)
                ->orderBy('order', 'asc')
                ->get()
                ->map(function ($chapter) {
                    return $chapter->details;
                }),
            'files_without_chapter' => $this->files()
                ->where('status', WebinarChapter::$chapterActive)
                ->orderBy('order', 'asc')
                ->whereNull('chapter_id')
                ->get()
                ->map(function ($file) {
                    return $file->details;
                }),
            'files_count' => $this->files()
                ->where('status', WebinarChapter::$chapterActive)
                ->count(),
            'text_lesson_chapters' => $this->chapters()
                ->whereHas('chapterItems', function ($query) {
                    $query->where('type', WebinarChapterItem::$chapterTextLesson);
                })
                ->where('status', WebinarChapter::$chapterActive)
                //   ->where('type', WebinarChapter::$chapterTextLesson)
                ->get()
                ->map(function ($chapter) {
                    return $chapter->details;
                }),
            'text_lessons_without_chapter' => $this->textLessons()
                ->where('status', WebinarChapter::$chapterActive)
                ->orderBy('order', 'asc')
                ->whereNull('chapter_id')
                ->get()
                ->map(function ($file) {
                    return $file->details;
                }),
            'text_lessons_count' => $this->chapters()
                ->whereHas('chapterItems', function ($query) {
                    $query->where('type', WebinarChapterItem::$chapterTextLesson);
                })
                // ->where('type', WebinarChapter::$chapterTextLesson)
                ->count(),

            'quizzes' => $this->quizzes()
                ->whereNull('chapter_id')
                ->where('status', 'active')
                ->get()
                ->map(function ($quiz) {
                    return $quiz->brief;
                }),
            'quizzes_count' => $this->quizzes->count(),

            'certificate' => $this->quizzes()->where('certificate', 1)->get()->map(function ($quiz) {
                return $quiz->brief;
            }),

            'auth_certificates' => $user ? $user->achievement_certificates($this) : [],

            'reviews' => $this->reviews->where('status', 'active')->map(function ($review) {
                return $review->details;
            }),

            'video_demo' => $this->video_demo ? url($this->video_demo) : null,
            'video_demo_source' => $this->video_demo_source,
            'image_cover' => $this->image_cover ? url($this->image_cover) : null,

            'tickets' => $this->tickets->map(function ($ticket) {
                return $ticket->details;
            }),
            'teacher' => $this->teacher->brief,
            'isDownloadable' => $this->isDownloadable() ? true : false,
            'teacher_is_offline' => $this->teacher->offline ? true : false,
            'tags' => $this->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'title' => $tag->title
                ];
            }),
            'auth_has_subscription' => ($user) ? $user->hasActiveSubscription : null,
            'can_add_to_cart' => $this->canAddToCart(),
            'can_buy_with_points' => ($this->canSale() and !$this->checkUserHasBought($user) and !empty($this->points) and $this->price > 0),
            'forum' => getFeaturesSettings("course_forum_status") == "1" ? $this->forum : 0,
            'auth_has_bought' => $hasBought,
            //////********************
        ];


        // return $details ;
        return array_merge($this->brief, $details);


    }

    public function getStudentsCountAttribute()
    {
        $studentsIds = Sale::where('webinar_id', $this->id)
            ->whereNull('refund_at')
            ->pluck('buyer_id')
            ->toArray();
        return count(array_unique($studentsIds));
    }

    public function pendingAssignments()
    {
        return $this->assignments()->where('status', 'active')
            ->whereHas('assignmentHistory', function ($query) {
                $query->where('status', 'pending');
            });
    }

    public function getQuizzesAverageGradeAttribute()
    {
        $quizzes = Quiz::where('webinar_id', $this->id)
            ->join('quizzes_results', 'quizzes_results.quiz_id', 'quizzes.id')
            ->select(DB::raw('avg(quizzes_results.user_grade) as result_grade'))
            ->whereIn('quizzes_results.status', ['passed', 'failed'])
            ->groupBy('quizzes_results.quiz_id')
            ->get();

        return $quizzes->avg('result_grade');
    }


    public function getAssignmentsAverageGradeAttribute()
    {
        $assignments = WebinarAssignment::where('webinar_id', $this->id)
            ->join('webinar_assignment_history', 'webinar_assignment_history.assignment_id', 'webinar_assignments.id')
            ->select(DB::raw('avg(webinar_assignment_history.grade) as result_grade'))
            ->whereIn('webinar_assignment_history.status', ['passed', 'not_passed'])
            ->groupBy('webinar_assignment_history.assignment_id')
            ->get();

        return $assignments->avg('result_grade') ?? 0;
    }

    public function getForumsMessagesCountAttribute()
    {
        $forums = CourseForum::where('webinar_id', $this->id)
            ->join('course_forum_answers', 'course_forum_answers.forum_id', 'course_forums.id')
            ->select(DB::raw('count(course_forum_answers.id) as count'))
            ->groupBy('course_forum_answers.forum_id')
            ->get();

        return $forums->sum('count') ?? 0;
    }

    public function getForumsStudentsCountAttribute($webinarId)
    {
        $forums = CourseForum::where('webinar_id', $webinarId)
            ->join('course_forum_answers', 'course_forum_answers.forum_id', 'course_forums.id')
            ->select(DB::raw('count(distinct course_forum_answers.user_id) as count'))
            ->groupBy('course_forum_answers.forum_id')
            ->get();

        return $forums->sum('count') ?? 0;
    }

    public function getSalesAmountAttribute()
    {
        return Sale::where('webinar_id', $this->id)
            ->whereNull('refund_at')
            ->sum('total_amount');
    }

    public function pendingQuizzes()
    {
        return $this->quizzes()->where('status', 'active')
            ->whereHas('quizResults', function ($query) {
                $query->where('status', 'waiting');
            });

    }

    public function getCommentsCountAttribute()
    {
        return Comment::where('webinar_id', $this->id)
            ->where('status', 'active')
            ->count();
    }

    public function getLabelAttribute()
    {
        switch ($this->status) {

            case self::$active :
                if ($this->isWebinar()) {
                    if ($this->start_date > time()) {
                        return trans('panel.not_conducted');
                    } elseif ($this->isProgressing()) {
                        return trans('webinars.in_progress');
                    } else {
                        return trans('public.finished');
                    }
                } else {
                    return trans('webinars.' . $this->type);
                }

            case self::$isDraft :
                return trans('public.draft');
            case self::$pending :
                return trans('public.waiting');
            case self::$inactive :
                return trans('public.rejected');
        }

    }

    public function getSpecificationAttribute()
    {
        $array = [];
        $nextSession = $this->nextSession();
        if ($this->isProgressing() and $nextSession) {
            $array['next_session_duration'] = $nextSession->duration;
            if ($this->isWebinar()) {
                $array['next_session_start_date'] = $nextSession->date;
            }
        } else {
            $array['duration'] = $this->duration;
            if ($this->isWebinar()) {
                $array['start_date'] = $this->start_date;
            }
        }
        if ($this->isTextCourse() or $this->isCourse()) {
            $array['files_count'] = $this->files->count();
        }
        if ($this->isTextCourse()) {
            $array['text_lessions_count'] = $this->textLessons->count();
        }
        if ($this->isCourse()) {
            $array['downloadable'] = (bool)$this->downloadable;
        }
        return $array;
    }
    public function scopeHandleFilters($query)
    {
        $request = request();
        $onlyNotConducted = $request->get('not_conducted');
        $offset = $request->get('offset', null);
        $limit = $request->get('limit', null);
        $upcoming = $request->get('upcoming', null);
        $isFree = $request->get('free', null);
        $withDiscount = $request->get('discount', null);
        $isDownloadable = $request->get('downloadable', null);
        $sort = $request->get('sort', null);
        $filterOptions = $request->get('filter_option', null);
        $type = $request->get('type', []);
        $moreOptions = $request->get('moreOptions', []);
        $category = $request->get('cat', null);
        $reward = $request->get('reward', null);

        if (!empty($reward) and $reward == 1) {
            $query->whereNotNull('points');
        }

        if (!empty($onlyNotConducted)) {
            $query->where('status', 'active')
                ->where('start_date', '>', time());
        }

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
            $specialOffersWebinarIds = SpecialOffer::where('status', 'active')
                ->where('from_date', '<', $now)
                ->where('to_date', '>', $now)
                ->pluck('webinar_id')
                ->toArray();

            $webinarIdsHasDiscount = array_merge($specialOffersWebinarIds, $webinarIdsHasDiscount);

            $webinarIdsHasDiscount = array_unique($webinarIdsHasDiscount);

            $query->whereIn("webinars.id", $webinarIdsHasDiscount);
        }


        if (!empty($filterOptions)) {
            $webinarIdsFilterOptions = WebinarFilterOption::where('filter_option_id', $filterOptions)
                ->pluck('webinar_id')
                ->toArray();

            $query->whereIn('webinars.id', $webinarIdsFilterOptions);
        }

        if (!empty($type)) {
            $query->where('type', $type);
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

        if (!empty($sort)) {
            if ($sort == 'expensive') {
                $query->orderBy('price', 'desc');
            }
            if ($sort == 'inexpensive') {
                $query->orderBy('price', 'asc');
            }

            if ($sort == 'bestsellers') {
                $query->leftJoin('sales', function ($join) {
                    $join->on('webinars.id', '=', 'sales.webinar_id')
                        ->whereNull('refund_at');
                })
                    ->whereNotNull('sales.webinar_id')
                    ->select('webinars.*', 'sales.webinar_id', DB::raw('count(sales.webinar_id) as salesCounts'))
                    ->groupBy('sales.webinar_id')
                    ->orderBy('salesCounts', 'desc');
            }

            if ($sort == 'best_rates') {
                $query->leftJoin('webinar_reviews', function ($join) {
                    $join->on('webinars.id', '=', 'webinar_reviews.webinar_id');
                    $join->where('webinar_reviews.status', 'active');
                })
                    ->whereNotNull('rates')
                    ->select('webinars.*', DB::raw('avg(rates) as rates'))
                    ->groupBy('webinars.id')
                    ->orderBy('rates', 'desc');
            }

            if ($sort == 'newest') {
                $query->orderBy('created_at', 'desc');
            }

        } else {
            $query->orderBy('webinars.created_at', 'desc')
                ->orderBy('webinars.updated_at', 'desc');
        }

        return $query;
    }

    public function scopeValidWebinar($query)
    {

        return $query->where('private', false)->where('status', 'active');
    }


    private function liveWebinarStatus()
    {
        $live_webinar_status = null;
        if ($this->type == 'webinar') {
            if ($this->start_date > time()) {
                $live_webinar_status = 'not_conducted';
            } elseif ($this->isProgressing()) {
                $live_webinar_status = 'in_progress';
            } else {
                $live_webinar_status = 'finished';
            }
        }
        return $live_webinar_status;

    }

    public function progress()
    {
        $user = apiAuth();
        /* progressbar status */
        $hasBought = $this->checkUserHasBought($user);
        $progress = null;

        if ($hasBought or $this->isWebinar()) {
            $progress = $this->getProgress();
        }

        return $progress;
    }

    public function getProgress($isLearningPage = false)
    {
        $progress = 0;

        $user = apiAuth();
        if (!$user and !$this->isWebinar()) {
            return null;
        }
        if ($this->isWebinar()) {
            if ($user and ($this->isProgressing() or $isLearningPage) and $this->checkUserHasBought($user)) {
                $user_id = $user->id;
                $sessions = $this->sessions;
                $files = $this->files;
                $passed = 0;

                foreach ($files as $file) {
                    $status = CourseLearning::where('user_id', $user_id)
                        ->where('file_id', $file->id)
                        ->first();

                    if (!empty($status)) {
                        $passed += 1;
                    }
                }

                foreach ($sessions as $session) {
                    $status = CourseLearning::where('user_id', $user_id)
                        ->where('session_id', $session->id)
                        ->first();

                    if (!empty($status)) {
                        $passed += 1;
                    }
                }

                if ($passed > 0) {
                    $progress = ($passed * 100) / ($sessions->count() + $files->count());

                    $this->handleLearningProgress100Reward($progress, $user_id, $this->id);
                }
            } else if (!empty($this->capacity)) {
                $salesCount = !empty($this->sales_count) ? $this->sales_count : $this->sales()->count();

                if ($salesCount > 0) {
                    $progress = ($salesCount * 100) / $this->capacity;
                }
            }
        } elseif ($this->checkUserHasBought($user)) {
            $user_id = $user->id;
            $files = $this->files;
            $textLessons = $this->textLessons;

            $passed = 0;

            foreach ($files as $file) {
                $status = CourseLearning::where('user_id', $user_id)
                    ->where('file_id', $file->id)
                    ->first();

                if (!empty($status)) {
                    $passed += 1;
                }
            }

            foreach ($textLessons as $textLesson) {
                $status = CourseLearning::where('user_id', $user_id)
                    ->where('text_lesson_id', $textLesson->id)
                    ->first();

                if (!empty($status)) {
                    $passed += 1;
                }
            }

            if ($passed > 0) {
                $progress = ($passed * 100) / ($files->count() + $textLessons->count());

                $this->handleLearningProgress100Reward($progress, $user_id, $this->id);
            }
        }

        return round($progress, 2);
    }

    private function isFavorite()
    {
        $user = apiAuth();
        $isFavorite = false;
        if (!empty($user)) {
            $isFavorite = Favorite::where('webinar_id', $this->id)
                ->where('user_id', $user->id)
                ->first();
        }
        return ($isFavorite) ? true : false;
    }

    public function purchasedDate()
    {
        $user = apiAuth();
        $sale = null;
        if ($user) {
            $sale = Sale::where('buyer_id', $user->id)
                ->whereNotNull('webinar_id')
                ->where('type', 'webinar')
                ->where('webinar_id', $this->id)
                ->whereNull('refund_at')
                ->first();
        }


        return ($sale) ? $sale->created_at : null;
    }

    public function contentItems()
    {
        // if($this->ty)
    }


    public function canAddToCart($user = null)
    {

        if (!apiAuth()) {
            return null;
        }
        if (!$this->price) {
            return 'free';
        }
        return $this->checkCourseForSale($user);

    }

    function checkCourseForSale($user = null)
    {
        $course = $this;
        $user = ($user) ?: apiAuth();


        if ($this->expired) return 'expired';

        if (!$this->hasCapacity) return 'no_capacity';

        if ($this->sameUser) return 'same_user';

        if ($course->checkUserHasBought($user)) return 'already_bought';

        if ($this->notPassedRequiredPrerequisite()) return 'required_prerequisites';

        return 'ok';
    }

    function checkCourseForSaleMsg($user = null)
    {
        $status = $this->checkCourseForSale();
        if ($status == 'expired') {
            return trans('cart.course_not_capacity');

        } elseif ($status == 'no_capacity') {
            return trans('cart.course_not_capacity');

        } elseif ($status == 'already_bought') {
            return trans('site.you_bought_webinar');

        } elseif ($status == 'same_user') {
            return trans('cart.cant_purchase_your_course');
        } elseif ($status == 'required_prerequisites') {
            return trans('cart.this_course_has_required_prerequisite');
        } elseif ($status == 'ok') {

        }
    }

    public function getExpiredAttribute()
    {
        if ($this->type == self::$webinar) {
            return ($this->start_date < time());
        }
        return false;
    }

    public function getHasCapacityAttribute()
    {

        $salesCount = !empty($this->sales_count) ? $this->sales_count : $this->sales()->count();
        if ($this->type == 'webinar') {
            return ($salesCount < $this->capacity);
        }
        return true;
    }

    public function getSameUserAttribute($user = null)
    {
        $user = $user ?: apiAuth();

        if ($this->creator_id == $user->id or $this->teacher_id == $user->id) {
            return true;
        }

        return false;
    }

    public function notPassedRequiredPrerequisite($user = null)
    {
        $user = $user ?: apiAuth();
        $isRequiredPrerequisite = false;
        $prerequisites = $this->prerequisites;
        if (count($prerequisites)) {
            foreach ($prerequisites as $prerequisite) {
                $prerequisiteWebinar = $prerequisite->prerequisiteWebinar;

                if ($prerequisite->required and !empty($prerequisiteWebinar) and !$prerequisiteWebinar->checkUserHasBought($user)) {
                    $isRequiredPrerequisite = true;
                }
            }
        }

        return $isRequiredPrerequisite;

    }


    public function tickets()
    {
        return $this->hasMany('App\Models\Api\Ticket', 'webinar_id', 'id');
    }

    public function badges()
    {
        return $this->hasMany('App\Models\Api\ProductBadgeContent', 'targetable_id', 'id');
    }

    public function chapters()
    {
        return $this->hasMany('App\Models\Api\WebinarChapter', 'webinar_id', 'id');
    }

    public function sessions()
    {
        return $this->hasMany('App\Models\Api\Session', 'webinar_id', 'id');
    }

    public function files()
    {
        return $this->hasMany('App\Models\Api\File', 'webinar_id', 'id');
    }

    public function textLessons()
    {
        return $this->hasMany('App\Models\Api\TextLesson', 'webinar_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo('App\Models\Api\User', 'creator_id', 'id');
    }

    public function teacher()
    {
        return $this->belongsTo('App\Models\Api\User', 'teacher_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

    public function tags()
    {
        return $this->hasMany('App\Models\Tag', 'webinar_id', 'id');
    }

    public function purchases()
    {
        return $this->hasMany('App\Models\Purchase', 'webinar_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Api\Comment', 'webinar_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\Api\WebinarReview', 'webinar_id', 'id');
    }

    public function prerequisites()
    {
        return $this->hasMany('App\Models\Api\Prerequisite', 'webinar_id', 'id');
    }

    public function faqs()
    {
        return $this->hasMany('App\Models\Api\Faq', 'webinar_id', 'id');
    }

    public function quizzes()
    {
        return $this->hasMany('App\Models\Api\Quiz', 'webinar_id', 'id');
    }
}
