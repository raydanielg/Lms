<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpcomingCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upcoming_courses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('creator_id')->unsigned();
            $table->integer('teacher_id')->unsigned();
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('webinar_id')->unsigned()->nullable()->comment('when assigned a course');
            $table->enum('type', ['webinar', 'course', 'text_lesson']);
            $table->string('slug')->unique()->index();
            $table->string('thumbnail');
            $table->string('image_cover');
            $table->string('video_demo')->nullable();
            $table->enum('video_demo_source', ['upload', 'youtube', 'vimeo', 'external_link'])->nullable();
            $table->bigInteger('publish_date')->unsigned()->nullable();
            $table->string('timezone')->nullable();
            $table->integer('points')->unsigned()->nullable();
            $table->integer('capacity')->unsigned()->nullable();
            $table->float('price', 15, 2)->nullable();
            $table->integer('duration')->unsigned()->nullable();
            $table->integer('sections')->unsigned()->nullable();
            $table->integer('parts')->unsigned()->nullable();
            $table->integer('course_progress')->unsigned()->nullable();
            $table->boolean('support')->default(false);
            $table->boolean('certificate')->default(false);
            $table->boolean('include_quizzes')->default(false);
            $table->boolean('downloadable')->default(false);
            $table->boolean('forum')->default(false);
            $table->text('message_for_reviewer')->nullable();
            $table->enum('status', ['active', 'pending', 'is_draft', 'inactive'])->default('is_draft');
            $table->bigInteger('created_at')->unsigned();

            $table->foreign('creator_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('teacher_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('category_id')->on('categories')->references('id')->cascadeOnDelete();
            $table->foreign('webinar_id')->on('webinars')->references('id')->nullOnDelete();
        });


        Schema::create('upcoming_course_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('upcoming_course_id');
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->text('seo_description')->nullable();
            $table->longText('description')->nullable();

            $table->foreign('upcoming_course_id')->on('upcoming_courses')->references('id')->onDelete('cascade');
        });

        Schema::create('upcoming_course_filter_option', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('upcoming_course_id')->unsigned();
            $table->integer('filter_option_id')->unsigned();

            $table->foreign('upcoming_course_id')->references('id')->on('upcoming_courses')->onDelete('cascade');;
            $table->foreign('filter_option_id')->references('id')->on('filter_options')->onDelete('cascade');;
        });

        Schema::create('upcoming_course_followers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('upcoming_course_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->bigInteger('created_at')->unsigned();

            $table->foreign('upcoming_course_id')->references('id')->on('upcoming_courses')->onDelete('cascade');;
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');;
        });

        Schema::create('upcoming_course_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('upcoming_course_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('reason');
            $table->text('message');
            $table->bigInteger('created_at')->unsigned();

            $table->foreign('upcoming_course_id')->references('id')->on('upcoming_courses')->onDelete('cascade');;
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');;
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->integer('upcoming_course_id')->unsigned()->nullable();

            $table->foreign('upcoming_course_id')->on('upcoming_courses')->references('id')->onDelete('cascade');
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->integer('upcoming_course_id')->unsigned()->nullable()->after('bundle_id');

            $table->foreign('upcoming_course_id')->on('upcoming_courses')->references('id')->onDelete('cascade');
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->integer('upcoming_course_id')->unsigned()->nullable()->after('bundle_id');

            $table->foreign('upcoming_course_id')->on('upcoming_courses')->references('id')->onDelete('cascade');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->integer('upcoming_course_id')->unsigned()->nullable()->after('bundle_id');

            $table->foreign('upcoming_course_id')->on('upcoming_courses')->references('id')->onDelete('cascade');
        });

        Schema::table('webinar_extra_descriptions', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `webinar_extra_descriptions` MODIFY COLUMN `webinar_id` int UNSIGNED NULL AFTER `creator_id`");
            $table->integer('upcoming_course_id')->unsigned()->nullable()->after('webinar_id');

            $table->foreign('upcoming_course_id')->on('upcoming_courses')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('upcoming_courses');
        Schema::dropIfExists('upcoming_course_translations');
        Schema::dropIfExists('upcoming_course_filter_option');
    }
}
