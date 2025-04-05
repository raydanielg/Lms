<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url')->index()->unique();
            $table->string('cover')->nullable();
            $table->string('image')->nullable();
            $table->boolean('enable_login')->default(false);
            $table->boolean('enable_resubmission')->default(false);
            $table->boolean('enable_welcome_message')->default(false);
            $table->boolean('enable_tank_you_message')->default(false);
            $table->string('welcome_message_image')->nullable();
            $table->string('tank_you_message_image')->nullable();
            $table->bigInteger('start_date')->unsigned()->nullable();
            $table->bigInteger('end_date')->unsigned()->nullable();
            $table->boolean("enable")->default(false);
            $table->bigInteger('created_at')->unsigned();
        });

        Schema::create('form_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_id')->unsigned();
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->string('heading_title')->nullable();
            $table->text('description')->nullable();
            $table->string('welcome_message_title')->nullable();
            $table->text('welcome_message_description')->nullable();
            $table->string('tank_you_message_title')->nullable();
            $table->text('tank_you_message_description')->nullable();

            $table->foreign('form_id')->on('forms')->references('id')->cascadeOnDelete();
        });

        Schema::create('form_roles_users_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_id')->unsigned();
            $table->integer('role_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('group_id')->unsigned()->nullable();

            $table->foreign('form_id')->on('forms')->references('id')->cascadeOnDelete();
            $table->foreign('role_id')->on('roles')->references('id')->cascadeOnDelete();
            $table->foreign('user_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('group_id')->on('groups')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forms');
        Schema::dropIfExists('form_translations');
        Schema::dropIfExists('form_roles_users_groups');
    }
}
