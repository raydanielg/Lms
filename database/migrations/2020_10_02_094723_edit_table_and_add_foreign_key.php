<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditTableAndAddForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->dropColumn('item_id');
            $table->integer('webinar_id')->after('user_id')->unsigned();

            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });

        Schema::table('comments', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->dropColumn('item_id');
            $table->integer('webinar_id')->unsigned();
            $table->boolean('report')->default(0);
            $table->boolean('disabled')->default(0);

            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });

        Schema::table('quizzes_results', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });

        Schema::table('quizzes_questions_answers', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->foreign('question_id')->references('id')->on('quizzes_questions')->onDelete('cascade');
            $table->foreign('creator_user_id')->references('id')->on('users')->onDelete('cascade');

        });

        Schema::table('quizzes_questions', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            $table->foreign('creator_user_id')->references('id')->on('users')->onDelete('cascade');

        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->dropColumn('item_id');
            $table->integer('webinar_id')->after('id')->unsigned();

            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
        });

        Schema::table('files', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->dropColumn('item_id');
            $table->integer('webinar_id')->after('id')->unsigned();

            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->dropColumn('item_id');
            $table->dropColumn('type');
            $table->integer('webinar_id')->after('id')->unsigned();

            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->dropColumn('item_id');
            $table->integer('webinar_id')->after('id')->unsigned();

            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->dropColumn('item_id');
            $table->integer('webinar_id')->after('id')->unsigned();

            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
        });


        Schema::table('quizzes', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->dropColumn('item_id');
            $table->integer('webinar_id')->nullable()->after('id')->unsigned();

            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
