<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('target_type', \App\Models\Installment::$targetTypes);
            $table->string('target')->nullable();
            $table->integer('capacity')->unsigned()->nullable();
            $table->bigInteger('start_date')->unsigned()->nullable();
            $table->bigInteger('end_date')->unsigned()->nullable();
            $table->boolean('verification')->default(false);
            $table->boolean('request_uploads')->default(false);
            $table->boolean('bypass_verification_for_verified_users')->default(false);
            $table->float('upfront', 15, 2)->nullable();
            $table->enum('upfront_type', ['fixed_amount', 'percent'])->nullable();
            $table->boolean('enable')->default(false);
            $table->bigInteger('created_at')->unsigned();
        });

        Schema::create('installment_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('installment_id')->unsigned();
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->string('main_title');
            $table->text('description');
            $table->string('banner')->nullable();
            $table->text('options')->nullable();
            $table->text('verification_description')->nullable();
            $table->string('verification_banner')->nullable();
            $table->string('verification_video')->nullable();

            $table->foreign('installment_id')->on('installments')->references('id')->cascadeOnDelete();
        });

        Schema::create('installment_specification_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('installment_id')->unsigned();
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('instructor_id')->unsigned()->nullable();
            $table->integer('seller_id')->unsigned()->nullable();
            $table->integer('webinar_id')->unsigned()->nullable();
            $table->integer('product_id')->unsigned()->nullable();
            $table->integer('bundle_id')->unsigned()->nullable();
            $table->integer('subscribe_id')->unsigned()->nullable();
            $table->integer('registration_package_id')->unsigned()->nullable();

            $table->foreign('installment_id')->on('installments')->references('id')->cascadeOnDelete();
            $table->foreign('category_id')->on('categories')->references('id')->cascadeOnDelete();
            $table->foreign('instructor_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('seller_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('webinar_id')->on('webinars')->references('id')->cascadeOnDelete();
            $table->foreign('product_id')->on('products')->references('id')->cascadeOnDelete();
            $table->foreign('bundle_id')->on('bundles')->references('id')->cascadeOnDelete();
            $table->foreign('subscribe_id')->on('subscribes')->references('id')->cascadeOnDelete();
            $table->foreign('registration_package_id')->on('registration_packages')->references('id')->cascadeOnDelete();
        });

        Schema::create('installment_steps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('installment_id')->unsigned();
            $table->integer('deadline')->unsigned()->nullable();
            $table->float('amount', 15, 2)->nullable();
            $table->enum('amount_type', ['fixed_amount', 'percent'])->nullable();
            $table->integer('order')->unsigned()->nullable();

            $table->foreign('installment_id')->on('installments')->references('id')->cascadeOnDelete();
        });

        Schema::create('installment_step_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('installment_step_id')->unsigned();
            $table->string('locale', 191)->index();
            $table->string('title');

            $table->foreign('installment_step_id')->on('installment_steps')->references('id')->cascadeOnDelete();
        });

        Schema::create('installment_user_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('installment_id')->unsigned();
            $table->integer('group_id')->unsigned()->nullable();

            $table->foreign('installment_id')->on('installments')->references('id')->cascadeOnDelete();
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
        Schema::dropIfExists('installments');
        Schema::dropIfExists('installment_translations');
        Schema::dropIfExists('installment_specification_items');
        Schema::dropIfExists('installment_steps');
        Schema::dropIfExists('installment_step_translations');
        Schema::dropIfExists('installment_user_groups');
    }
}
