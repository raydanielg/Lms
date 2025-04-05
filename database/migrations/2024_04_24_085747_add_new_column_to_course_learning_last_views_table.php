<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_learning_last_views', function (Blueprint $table) {
            $table->integer('webinar_id')->unsigned()->after('user_id');

            $table->foreign('webinar_id')->on('webinars')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_learning_last_views', function (Blueprint $table) {
            //
        });
    }
};
