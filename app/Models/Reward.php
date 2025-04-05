<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    public $timestamps = false;
    protected $table = 'rewards';
    protected $guarded = ['id'];

    const ACCOUNT_CHARGE = 'account_charge';
    const CREATE_CLASSES = 'create_classes';
    const BUY = 'buy';
    const PASS_THE_QUIZ = 'pass_the_quiz';
    const CERTIFICATE = 'certificate';
    const COMMENT = 'comment';
    const REGISTER = 'register';
    const REVIEW_COURSES = 'review_courses';
    const INSTRUCTOR_MEETING_RESERVE = 'instructor_meeting_reserve';
    const STUDENT_MEETING_RESERVE = 'student_meeting_reserve';
    const NEWSLETTERS = 'newsletters';
    const BADGE = 'badge';
    const REFERRAL = 'referral';
    const LEARNING_PROGRESS_100 = 'learning_progress_100';
    const CHARGE_WALLET = 'charge_wallet';
    const BUY_STORE_PRODUCT = 'buy_store_product';
    const PASS_ASSIGNMENT = 'pass_assignment';
    const MAKE_TOPIC = 'make_topic';
    const SEND_TOPIC_POST = 'send_post_in_topic';
    const CREATE_BLOG_BY_INSTRUCTOR = 'create_blog_by_instructor';
    const COMMENT_FOR_INSTRUCTOR_BLOG = 'comment_for_instructor_blog';

    public static function getTypesLists(): array
    {
        return [
            self::ACCOUNT_CHARGE,
            self::CREATE_CLASSES,
            self::BUY,
            self::PASS_THE_QUIZ,
            self::CERTIFICATE,
            self::COMMENT,
            self::REGISTER,
            self::REVIEW_COURSES,
            self::INSTRUCTOR_MEETING_RESERVE,
            self::STUDENT_MEETING_RESERVE,
            self::NEWSLETTERS,
            self::BADGE,
            self::REFERRAL,
            self::LEARNING_PROGRESS_100,
            self::CHARGE_WALLET,
            self::BUY_STORE_PRODUCT,
            self::PASS_ASSIGNMENT,
            self::MAKE_TOPIC,
            self::SEND_TOPIC_POST,
            self::CREATE_BLOG_BY_INSTRUCTOR,
            self::COMMENT_FOR_INSTRUCTOR_BLOG,
        ];
    }
}
