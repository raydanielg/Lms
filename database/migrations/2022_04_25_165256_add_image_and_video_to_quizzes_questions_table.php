<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageAndVideoToQuizzesQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quizzes_questions', function (Blueprint $table) {
            $table->string('image')->nullable()->after('type');
            $table->string('video')->nullable()->after('image');
        });
    }
}
