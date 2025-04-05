<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ConvertCreatoreUserIdToCreatorId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            DB::statement("ALTER TABLE `webinars` DROP FOREIGN KEY `webinars_creator_user_id_foreign`;");
            DB::statement("ALTER TABLE `webinars` CHANGE `creator_user_id` `creator_id` INTEGER UNSIGNED NOT NULL;");


            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('webinar_reviews', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            DB::statement("ALTER TABLE `webinar_reviews` DROP FOREIGN KEY `webinar_reviews_creator_user_id_foreign`;");
            DB::statement("ALTER TABLE `webinar_reviews` CHANGE `creator_user_id` `creator_id` INTEGER UNSIGNED NOT NULL;");

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('quizzes_questions_answers', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            DB::statement("ALTER TABLE `quizzes_questions_answers` DROP FOREIGN KEY `quizzes_questions_answers_creator_user_id_foreign`;");
            DB::statement("ALTER TABLE `quizzes_questions_answers` CHANGE `creator_user_id` `creator_id` INTEGER UNSIGNED NOT NULL;");

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('quizzes_questions', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            DB::statement("ALTER TABLE `quizzes_questions` DROP FOREIGN KEY `quizzes_questions_creator_user_id_foreign`;");
            DB::statement("ALTER TABLE `quizzes_questions` CHANGE `creator_user_id` `creator_id` INTEGER UNSIGNED NOT NULL;");

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            DB::statement("ALTER TABLE `quizzes` CHANGE `creator_user_id` `creator_id` INTEGER UNSIGNED NOT NULL;");

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->integer('organ_id')->nullable()->after('role_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('creator_id', function (Blueprint $table) {
            //
        });
    }
}
