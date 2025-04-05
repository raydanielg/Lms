<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoticeboardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('noticeboards', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('organ_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->enum('type', \App\Models\Noticeboard::$migrateTypes);
            $table->string('sender');
            $table->string('title');
            $table->text('message');
            $table->integer('created_at')->unsigned();

            $table->foreign('organ_id')->on('users')->references('id')->onDelete('cascade');
            $table->foreign('user_id')->on('users')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('noticeboards');
    }
}
