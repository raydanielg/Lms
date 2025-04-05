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
        Schema::create('abandoned_cart_rule_users_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('abandoned_cart_rule_id')->unsigned();
            $table->integer('group_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();

            $table->foreign('abandoned_cart_rule_id', 'abandoned_cart_rule_id')->on('abandoned_cart_rules')->references('id')->cascadeOnDelete();
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
        Schema::dropIfExists('abandoned_cart_rule_users_groups');
    }
};
