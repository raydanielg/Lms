<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebinarAssignmentHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webinar_assignment_history', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('instructor_id')->unsigned();
            $table->integer('student_id')->unsigned();
            $table->integer('assignment_id')->unsigned();
            $table->integer('grade')->nullable()->unsigned();
            $table->enum('status', \App\Models\WebinarAssignmentHistory::$assignmentHistoryStatus);
            $table->bigInteger('created_at')->unsigned();

            $table->foreign('instructor_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('student_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('assignment_id')->on('webinar_assignments')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webinar_assignment_history');
    }
}
