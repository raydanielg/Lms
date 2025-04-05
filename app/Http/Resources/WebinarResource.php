<?php

namespace App\Http\Resources;

use App\Mixins\Cashback\CashbackRules;
use Illuminate\Http\Resources\Json\JsonResource;

class WebinarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public $statistic = false;
    public $web = false;

    public function toArray($request)
    {
        $user = apiAuth();
        $purchase = $user ? $user->purchases()->where('webinar_id', $this->id)->first() : null;

        $cashbackRules = null;
        if (!empty($this->price) and getFeaturesSettings('cashback_active') and (empty($user) or !$user->disable_cashback)) {
            $cashbackRulesMixin = new CashbackRules($user);
            $cashbackRules = $cashbackRulesMixin->getRules('courses', $this->id, $this->type, $this->category->id, $this->teacher->id);
        }

        return [
            'id' => $this->id,
            'image' => url($this->getImage()),
            'image_cover' => $this->image_cover ? url($this->image_cover) : null,
            'status' => $this->status,
            'title' => $this->title,
            'can' => [
                'view' => !$this->can_view_error(),
            ],
            'reviews_count' => $this->reviews->pluck('creator_id')->count(),
            'can_view_error' => $this->can_view_error(),
            'type' => $this->type,
            // 'type' => trans('webinars.'.$this->type),
            'link' => $this->getUrl(),
            'label' => $this->label,
            'progress' => $this->progress(),
            'progress_percent' => $this->getProgress(),
            'price' => convertPriceToUserCurrency($this->price),
            'best_ticket' => $this->bestTicket(),
            'active_special_offer' => $this->activeSpecialOffer() ?: null,
            'rate' => $this->getRate(),
            'access_days' => $this->access_days,
            $this->mergeWhen($purchase, function () use ($purchase) {
                return [
                    'expired' => ($this->access_days and !$this->checkHasExpiredAccessDays($purchase->created_at)),
                    'expire_on' => $this->getExpiredAccessDays($purchase->created_at) ?: null,
                ];
            }),
            'category' => $this->category->title,
            // 'sales_amount' => ($this->sales) ? $this->sales->sum('amount') : 0,
            'sales_amount' => $this->sales_amount,
            'sales_count' => $this->sales->count(),
            'created_at' => $this->created_at,
            'purchased_at' => $this->purchasedDate(),
            'start_date' => $this->start_date,
            'duration' => $this->duration,
            'specification' => $this->specification,

            'teacher' => $this->teacher->brief,
            'capacity' => $this->capacity,
            'cashbackRules' => $cashbackRules,
            $this->mergeWhen($this->statistic, function () {
                return [
                    'students_count' => $this->students_count,
                    'comments_count' => $this->comments_count,
                    'chapters_count' => $this->chapters->count(),
                    'sessions_count' => $this->sessions->count(),
                    'pending_quizzes_count' => $this->pendingQuizzes->count(),
                    'pending_assignments_count' => $this->pendingAssignments->count(),
                    'rates_count' => $this->reviews->count(),
                    'quizzes_count' => $this->quizzes->count(),
                    'quizzes_average_grade' => $this->quizzes_average_grade,
                    'assignments_count' => $this->assignments->count(),
                    'assignments_average_grade' => $this->assignments_average_grade,
                    'forums_messages_count' => $this->forums_messages_count,
                    'forums_students_count' => $this->forums_students_count,

                    'students_roles' => $this->students_roles,
                    'quizzes_result_status' => $this->quiz_status,
                    'assignments_status' => $this->assignments_status,
                    'monthly_sales' => $this->monthly_sales,
                    'course_progress_line' => $this->course_progress_line,
                    'course_progress' => $this->course_progress,
                    'students' => $this->getStudents()


                ];
            }),
            $this->when($this->web, function () {
                return [
                    'rate_type' => [
                        'content_quality' => $this->reviews->count() > 0 ? round($this->reviews->avg('content_quality'), 1) : 0,
                        'instructor_skills' => $this->reviews->count() > 0 ? round($this->reviews->avg('instructor_skills'), 1) : 0,
                        'purchase_worth' => $this->reviews->count() > 0 ? round($this->reviews->avg('purchase_worth'), 1) : 0,
                        'support_quality' => $this->reviews->count() > 0 ? round($this->reviews->avg('support_quality'), 1) : 0,

                    ],
                    'reviews_count' => $this->reviews->pluck('creator_id')->count(),
                    //    'teacher',
                    'video_demo' => $this->video_demo,


                ];
            }),
            'description' => $this->description,
            'sales_count_number' => $this->sales_count_number,


        ];
    }
}
