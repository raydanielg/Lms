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
        Schema::create('purchase_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('start_at')->nullable();
            $table->bigInteger('end_at')->nullable();
            $table->integer('popup_duration')->unsigned()->nullable();
            $table->integer('popup_delay')->unsigned()->nullable();
            $table->integer('maximum_purchase_amount')->unsigned()->nullable();
            $table->integer('maximum_community_age')->unsigned()->nullable();
            $table->enum('display_type', ['overall', 'per_session']);
            $table->integer('display_time')->unsigned()->nullable();
            $table->boolean('display_for_logged_out_users')->default(false);
            $table->boolean('enable')->default(false);
            $table->bigInteger('created_at');
        });

        Schema::create('purchase_notification_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('purchase_notification_id')->unsigned();
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->string('popup_title');
            $table->string('popup_subtitle');
            $table->text('users');
            $table->text('times');

            $table->foreign('purchase_notification_id', 'purchase_notification_id_trans')->on('purchase_notifications')->references('id')->cascadeOnDelete();
        });


        Schema::create('purchase_notification_roles_groups_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('purchase_notification_id')->unsigned();
            $table->integer('role_id')->unsigned()->nullable();
            $table->integer('group_id')->unsigned()->nullable();
            $table->integer('webinar_id')->unsigned()->nullable();
            $table->integer('bundle_id')->unsigned()->nullable();
            $table->integer('product_id')->unsigned()->nullable();

            $table->foreign('purchase_notification_id', 'purchase_notification_id_role_group')->on('purchase_notifications')->references('id')->cascadeOnDelete();
            $table->foreign('role_id')->on('roles')->references('id')->cascadeOnDelete();
            $table->foreign('group_id')->on('groups')->references('id')->cascadeOnDelete();
            $table->foreign('webinar_id')->on('webinars')->references('id')->cascadeOnDelete();
            $table->foreign('bundle_id')->on('bundles')->references('id')->cascadeOnDelete();
            $table->foreign('product_id')->on('products')->references('id')->cascadeOnDelete();
        });

        Schema::create('purchase_notification_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('purchase_notification_id')->unsigned();
            $table->enum('display_type', ['overall', 'per_session']);
            $table->integer('count_view')->unsigned()->default(0);
            $table->boolean('session_ended')->default(false)->comment("Get True After the user login, we update all the per_session records");

            $table->foreign('purchase_notification_id', 'purchase_notification_id_history')->on('purchase_notifications')->references('id')->cascadeOnDelete();
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
        Schema::dropIfExists('purchase_notifications');
        Schema::dropIfExists('purchase_notification_translations');
        Schema::dropIfExists('purchase_notification_roles_groups_contents');
        Schema::dropIfExists('purchase_notification_histories');
    }
};
