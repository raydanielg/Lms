<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditStatusCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->integer('review_id')->unsigned()->nullable()->after('user_id');
            DB::statement("ALTER TABLE `comments` MODIFY COLUMN `status` ENUM('pending', 'active') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `comment`");
            DB::statement("ALTER TABLE `comments` MODIFY COLUMN `webinar_id` int(0) UNSIGNED NULL AFTER `user_id`");

            $table->foreign('review_id')->references('id')->on('webinar_reviews')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('review_id');
        });
    }
}
