<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('group_id')->unsigned()->nullable();
            $table->integer('webinar_id')->unsigned()->nullable();
            $table->string('title');
            $table->text('message');
            $table->enum('sender', ['system', 'admin'])->nullable()->default('system');
            $table->enum('type', \App\Models\Notification::$notificationsType);
            $table->integer('created_at')->unsigned();

            $table->foreign('user_id')->on('users')->references('id')->onDelete('cascade');
            $table->foreign('group_id')->on('groups')->references('id')->onDelete('cascade');
            $table->foreign('webinar_id')->on('webinars')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
