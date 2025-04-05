<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    protected $table = 'home_sections';
    public $timestamps = false;
    protected $guarded = ['id'];

    static $names = [
        'featured_classes',
        'latest_bundles',
        'latest_classes',
        'best_rates',
        'trend_categories',
        'full_advertising_banner',
        'best_sellers',
        'discount_classes',
        'free_classes',
        'store_products',
        'testimonials',
        'subscribes',
        'find_instructors',
        'reward_program',
        'become_instructor',
        'forum_section',
        'video_or_image_section',
        'instructors',
        'half_advertising_banner',
        'organizations',
        'blog',
        'upcoming_courses',
    ];

    static $featured_classes = 'featured_classes';
    static $latest_bundles = 'latest_bundles';
    static $latest_classes = 'latest_classes';
    static $best_rates = 'best_rates';
    static $trend_categories = 'trend_categories';
    static $full_advertising_banner = 'full_advertising_banner';
    static $best_sellers = 'best_sellers';
    static $discount_classes = 'discount_classes';
    static $free_classes = 'free_classes';
    static $store_products = 'store_products';
    static $testimonials = 'testimonials';
    static $subscribes = 'subscribes';
    static $find_instructors = 'find_instructors';
    static $reward_program = 'reward_program';
    static $become_instructor = 'become_instructor';
    static $forum_section = 'forum_section';
    static $video_or_image_section = 'video_or_image_section';
    static $instructors = 'instructors';
    static $half_advertising_banner = 'half_advertising_banner';
    static $organizations = 'organizations';
    static $blog = 'blog';
    static $upcoming_courses = 'upcoming_courses';
}
