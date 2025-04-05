<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddNewTypeToRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rewards', function (Blueprint $table) {
            DB::statement("ALTER TABLE `rewards` MODIFY COLUMN `type` enum('account_charge', 'create_classes', 'buy', 'pass_the_quiz', 'certificate', 'comment', 'register', 'review_courses', 'instructor_meeting_reserve', 'student_meeting_reserve', 'newsletters', 'badge', 'referral', 'learning_progress_100', 'charge_wallet', 'buy_store_product') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `id`");
        });
    }
}
