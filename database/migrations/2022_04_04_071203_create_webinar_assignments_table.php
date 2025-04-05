<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebinarAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webinar_assignments', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('creator_id')->unsigned();
            $table->integer('webinar_id')->unsigned();
            $table->integer('chapter_id')->unsigned();
            $table->integer('grade')->unsigned()->nullable();
            $table->integer('pass_grade')->unsigned()->nullable();
            $table->integer('deadline')->unsigned()->nullable();
            $table->integer('attempts')->unsigned()->nullable();
            $table->boolean('check_previous_parts')->default(false);
            $table->integer('access_after_day')->unsigned()->nullable();
            $table->enum('status', ['active', 'inactive']);
            $table->bigInteger('created_at')->unsigned();

            $table->foreign('creator_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('webinar_id')->on('webinars')->references('id')->cascadeOnDelete();
            $table->foreign('chapter_id')->on('webinar_chapters')->references('id')->cascadeOnDelete();
        });

        Schema::create('webinar_assignment_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->string('locale', 191)->index();
            $table->integer('webinar_assignment_id')->unsigned();
            $table->string('title');
            $table->text('description');

            $table->foreign('webinar_assignment_id', 'webinar_assignment_id_translate_foreign')->on('webinar_assignments')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webinar_assignments');
        Schema::dropIfExists('webinar_assignment_translations');
    }
}
