<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_login_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('browser')->nullable();
            $table->string('device')->nullable();
            $table->string('os')->nullable();
            $table->string('ip')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->point('location')->nullable();
            $table->text('session_id');
            $table->bigInteger('session_start_at')->unsigned()->nullable();
            $table->bigInteger('session_end_at')->unsigned()->nullable();
            $table->enum("end_session_type", ['default', 'by_admin', 'by_user'])->nullable()->default(null);
            $table->bigInteger('created_at')->unsigned();

            $table->foreign('user_id')->on('users')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_logs');
    }
};
