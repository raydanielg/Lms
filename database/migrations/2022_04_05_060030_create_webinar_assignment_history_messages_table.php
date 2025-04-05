<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebinarAssignmentHistoryMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webinar_assignment_history_messages', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('assignment_history_id')->unsigned();
            $table->integer('sender_id')->unsigned();
            $table->text('message');
            $table->string('file_title')->nullable();
            $table->string('file_path')->nullable();
            $table->bigInteger('created_at')->unsigned();

            $table->foreign('assignment_history_id', 'webinar_assignment_history_id')->on('webinar_assignment_history')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webinar_assignment_history_messages');
    }
}
