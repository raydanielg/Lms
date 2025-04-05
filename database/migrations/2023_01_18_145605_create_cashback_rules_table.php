<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashbackRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashback_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('target_type', \App\Models\CashbackRule::$targetTypes);
            $table->string('target')->nullable();
            $table->bigInteger('start_date')->unsigned()->nullable();
            $table->bigInteger('end_date')->unsigned()->nullable();
            $table->float('amount', 15, 2)->nullable();
            $table->enum('amount_type', ['fixed_amount', 'percent'])->nullable();
            $table->boolean('apply_cashback_per_item')->default(false);
            $table->float('max_amount', 15, 2)->nullable();
            $table->float('min_amount', 15, 2)->nullable();
            $table->boolean('enable')->default(false);
            $table->bigInteger('created_at')->unsigned();
        });

        Schema::create('cashback_rule_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cashback_rule_id')->unsigned();
            $table->string('locale', 191)->index();
            $table->string('title');

            $table->foreign('cashback_rule_id')->on('cashback_rules')->references('id')->cascadeOnDelete();
        });

        Schema::create('cashback_rule_specification_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cashback_rule_id')->unsigned();
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('instructor_id')->unsigned()->nullable();
            $table->integer('seller_id')->unsigned()->nullable();
            $table->integer('webinar_id')->unsigned()->nullable();
            $table->integer('product_id')->unsigned()->nullable();
            $table->integer('bundle_id')->unsigned()->nullable();
            $table->integer('subscribe_id')->unsigned()->nullable();
            $table->integer('registration_package_id')->unsigned()->nullable();

            $table->foreign('cashback_rule_id')->on('cashback_rules')->references('id')->cascadeOnDelete();
            $table->foreign('category_id')->on('categories')->references('id')->cascadeOnDelete();
            $table->foreign('instructor_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('seller_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('webinar_id')->on('webinars')->references('id')->cascadeOnDelete();
            $table->foreign('product_id')->on('products')->references('id')->cascadeOnDelete();
            $table->foreign('bundle_id')->on('bundles')->references('id')->cascadeOnDelete();
            $table->foreign('subscribe_id')->on('subscribes')->references('id')->cascadeOnDelete();
            $table->foreign('registration_package_id', 'rules_registration_package_id')->on('registration_packages')->references('id')->cascadeOnDelete();
        });

        Schema::create('cashback_rule_users_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cashback_rule_id')->unsigned();
            $table->integer('group_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();

            $table->foreign('cashback_rule_id')->on('cashback_rules')->references('id')->cascadeOnDelete();
            $table->foreign('group_id')->on('groups')->references('id')->cascadeOnDelete();
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
        Schema::dropIfExists('cashback_rules');
        Schema::dropIfExists('cashback_rule_translations');
        Schema::dropIfExists('cashback_rule_specification_items');
        Schema::dropIfExists('cashback_rule_users_groups');
    }
}
