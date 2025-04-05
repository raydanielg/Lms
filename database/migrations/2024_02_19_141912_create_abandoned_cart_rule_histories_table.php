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
        Schema::create('abandoned_cart_rule_histories', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("user_id")->unsigned();
            $table->integer('cart_rule_id')->unsigned()->nullable();
            $table->enum("rule_action", ['send_reminder', 'send_coupon']);
            $table->enum("type", ['auto', 'manual']);
            $table->bigInteger("created_at")->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('abandoned_cart_rule_histories');
    }
};
