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
        Schema::table("abandoned_cart_rules", function (Blueprint $table) {
            $table->enum('target_type', \App\Models\AbandonedCartRule::$targetTypes)->default('all')->after('id');
            $table->string('target')->nullable()->after('target_type');
            $table->boolean('repeat_action')->default(false)->after('action_cycle');
            $table->integer('repeat_action_count')->unsigned()->nullable()->after('repeat_action');
        });

        Schema::create('abandoned_cart_rule_specification_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('abandoned_cart_rule_id')->unsigned();
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('instructor_id')->unsigned()->nullable();
            $table->integer('seller_id')->unsigned()->nullable();
            $table->integer('webinar_id')->unsigned()->nullable();
            $table->integer('product_id')->unsigned()->nullable();
            $table->integer('bundle_id')->unsigned()->nullable();

            $table->foreign('abandoned_cart_rule_id', 'abandoned_cart_rule_id_foreign')->on('abandoned_cart_rules')->references('id')->cascadeOnDelete();
            $table->foreign('category_id')->on('categories')->references('id')->cascadeOnDelete();
            $table->foreign('instructor_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('seller_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('webinar_id')->on('webinars')->references('id')->cascadeOnDelete();
            $table->foreign('product_id')->on('products')->references('id')->cascadeOnDelete();
            $table->foreign('bundle_id')->on('bundles')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('abandoned_cart_rule_specification_items');
    }
};
