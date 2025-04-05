<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddNewTypeInstructorBlogInBadgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('badges', function (Blueprint $table) {
            DB::statement("ALTER TABLE `badges` MODIFY COLUMN `type` enum('register_date', 'course_count', 'course_rate', 'sale_count', 'support_rate', 'product_sale_count', 'make_topic', 'send_post_in_topic', 'instructor_blog') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `image`;");
        });
    }
}
