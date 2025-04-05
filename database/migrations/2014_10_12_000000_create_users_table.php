<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('full_name', 128)->nullable();
            $table->string('role_name',64);
            $table->integer('role_id')->unsigned();
            $table->string('avatar', 64)->nullable();
            $table->string('mobile')->unique()->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('google_id')->nullable();
            $table->string('facebook_id')->nullable();
            $table->string('remember_token')->nullable();
            $table->enum('status', ['active','pending', 'inactive'])->default('active');
            $table->integer('created_at');
            $table->integer('updated_at')->nullable();
            $table->integer('deleted_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
