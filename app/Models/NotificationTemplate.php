<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $table = 'notification_templates';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    static $templateKeys = [
        'email' => '[u.email]',
        'mobile' => '[u.mobile]',
        'real_name' => '[u.name]',
        'real_name_2' => '[u.name.2]',
        'user_role' => '[u.role]',
        'instructor_name' => '[instructor.name]',
        'organization_name' => '[organization.name]',
        'student_name' => '[student.name]',
        'item_title' => '[item_title]',
        'group_title' => '[u.g.title]',
        'badge_title' => '[u.b.title]',
        'course_title' => '[c.title]',
        'quiz_title' => '[q.title]',
        'quiz_result' => '[q.result]',
        'support_ticket_title' => '[s.t.title]',
        'contact_us_title' => '[c.u.title]',
        'contact_us_message' => '[c.u.message]',
        'time_and_date' => '[time.date]',
        'time_and_date_2' => '[time.date.2]',
        'link' => '[link]',
        'rate_count' => '[rate.count]',
        'amount' => '[amount]',
        'payout_account' => '[payout.account]',
        'financial_doc_desc' => '[f.d.description]',
        'financial_doc_type' => '[f.d.type]',
        'subscribe_plan_name' => '[s.p.name]',
        'promotion_plan_name' => '[p.p.name]',
        'product_title' => '[p.title]',
        'assignment_grade' => '[assignment_grade]',
        'topic_title' => '[topic_title]',
        'forum_title' => '[forum_title]',
        'blog_title' => '[blog_title]',
        'installment_title' => '[installment_title]',
        'gift_title' => '[gift_title]',
        'gift_type' => '[gift_type]',
        'gift_message' => '[gift_message]',
        'content_type' => '[content_type]',
        'points' => '[points]',
        'form_title' => '[form_title]',
        'discount_title' => '[discount_title]',
        'discount_code' => '[discount_code]',
        'discount_amount' => '[discount_amount]',
    ];

    static $notificationTemplateAssignSetting = [
        'admin' => ['new_comment_admin', 'support_message_admin', 'support_message_replied_admin', 'promotion_plan_admin', 'payout_request_admin',
            'new_registration', 'new_become_instructor_request', 'new_course_enrollment', 'new_forum_topic', 'new_report_item_for_admin', 'new_item_created',
            'new_store_order', 'subscription_plan_activated', 'content_review_request', 'new_user_blog_post', 'new_user_item_rating', 'new_organization_user',
            'user_wallet_charge', 'new_user_payout_request', 'new_offline_payment_request'
        ],
        'user' => ['new_badge', 'change_user_group', 'user_access_to_content', 'new_referral_user', 'user_role_change', 'add_to_user_group', 'become_instructor_request_approved', 'become_instructor_request_rejected'],
        'course' => ['course_created', 'course_approve', 'course_reject', 'new_comment', 'support_message', 'support_message_replied', 'new_rating', 'new_question_in_forum', 'new_answer_in_forum'],
        'financial' => ['new_financial_document', 'payout_request', 'payout_proceed', 'offline_payment_request', 'offline_payment_approved', 'offline_payment_rejected', 'user_get_cashback', 'user_get_cashback_notification_for_admin'],
        'sale_purchase' => ['new_sales', 'new_purchase'],
        'plans' => ['new_subscribe_plan', 'promotion_plan'],
        'appointment' => ['new_appointment', 'new_appointment_link', 'appointment_reminder', 'meeting_finished', 'new_appointment_session'],
        'quiz' => ['new_certificate', 'waiting_quiz', 'waiting_quiz_result', 'new_quiz'],
        'store' => ['product_new_sale', 'product_new_purchase', 'product_new_comment', 'product_tracking_code', 'product_new_rating', 'product_receive_shipment', 'product_out_of_stock'],
        'assignment' => ['student_send_message', 'instructor_send_message', 'instructor_set_grade'],
        'topic' => ['send_post_in_topic'],
        'blog' => ['publish_instructor_blog_post', 'new_comment_for_instructor_blog_post'],
        'reminders' => ['webinar_reminder', 'meeting_reserve_reminder', 'subscribe_reminder'],
        'installments' => ['reminder_installments_before_overdue', 'installment_due_reminder', 'reminder_installments_after_overdue', 'approve_installment_verification_request', 'reject_installment_verification_request', 'paid_installment_step', 'paid_installment_step_for_admin', 'paid_installment_upfront', 'installment_verification_request_sent', 'admin_installment_verification_request_sent', 'instalment_request_submitted', 'instalment_request_submitted_for_admin'],
        'gifts' => ['reminder_gift_to_receipt', "gift_sender_confirmation", 'gift_sender_notification', 'admin_gift_submission', 'admin_gift_sending_confirmation'],
        'upcoming' => ['upcoming_course_submission', 'upcoming_course_submission_for_admin', 'upcoming_course_approved', 'upcoming_course_rejected', 'upcoming_course_published', 'upcoming_course_followed', 'upcoming_course_published_for_followers'],
        'bundles' => ['bundle_submission', 'bundle_submission_for_admin', 'bundle_approved', 'bundle_rejected', 'new_review_for_bundle'],
        'registration_bonus' => ['registration_bonus_achieved', 'registration_bonus_unlocked', 'registration_bonus_unlocked_for_admin'],
        'registration_package' => ['registration_package_activated', 'registration_package_activated_for_admin', 'registration_package_expired'],
        'misc' => ['contact_message_submission', 'contact_message_submission_for_admin', 'waitlist_submission', 'waitlist_submission_for_admin', 'user_get_new_point', 'new_course_notice'],
        'forms' => ['submit_form_by_users'],
    ];
}
