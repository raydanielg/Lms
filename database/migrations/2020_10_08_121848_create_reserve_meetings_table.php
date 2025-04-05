<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReserveMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reserve_meetings', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('meeting_id')->unsigned();
            $table->integer('meeting_time_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('request_time')->unsigned();
            $table->integer('paid_amount')->unsigned();
            $table->string('link')->nullable();
            $table->string('password',64)->nullable();
            $table->enum('status', ['open', 'finished']);
            $table->integer('created_at');

            $table->foreign('meeting_id')->references('id')->on('meetings')->onDelete('cascade');
            $table->foreign('meeting_time_id')->references('id')->on('meeting_times')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reserve_meetings');
    }
}
