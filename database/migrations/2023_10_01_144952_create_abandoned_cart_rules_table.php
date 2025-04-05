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
        Schema::create('abandoned_cart_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('action', ['send_reminder', 'send_coupon']);
            $table->integer('discount_id')->unsigned()->nullable();
            $table->integer('action_cycle')->unsigned();
            $table->float('minimum_cart_amount', 15, 2)->unsigned()->nullable();
            $table->float('maximum_cart_amount', 15, 2)->unsigned()->nullable();
            $table->bigInteger('start_at')->unsigned()->nullable();
            $table->bigInteger('end_at')->unsigned()->nullable();
            $table->boolean('enable')->default(false);
            $table->bigInteger('created_at')->unsigned();

            $table->foreign('discount_id')->on('discounts')->references('id')->cascadeOnDelete();
        });

        Schema::create('abandoned_cart_rule_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('abandoned_cart_rule_id')->unsigned();
            $table->string('locale', 191)->index();
            $table->string('title');

            $table->foreign('abandoned_cart_rule_id', 'abandoned_cart_rule_id_trans')->on('abandoned_cart_rules')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('abandoned_cart_rules');
        Schema::dropIfExists('abandoned_cart_rule_translations');
    }
};
