<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstructorIdToNoticeboardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('noticeboards', function (Blueprint $table) {
            $table->integer('instructor_id')->unsigned()->nullable()->after('organ_id');
            $table->integer('webinar_id')->unsigned()->nullable()->after('instructor_id');

            $table->foreign('instructor_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('webinar_id')->on('webinars')->references('id')->cascadeOnDelete();
        });
    }
}
