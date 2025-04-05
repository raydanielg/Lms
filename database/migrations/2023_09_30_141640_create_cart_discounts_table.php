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
        Schema::create('cart_discounts', function (Blueprint $table) {
            $table->increments("id");
            $table->integer('discount_id')->unsigned();
            $table->boolean('show_only_on_empty_cart')->default(false);
            $table->boolean('enable')->default(false);
            $table->bigInteger('created_at')->unsigned();

            $table->foreign('discount_id')->on('discounts')->references('id')->cascadeOnDelete();
        });

        Schema::create('cart_discount_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cart_discount_id')->unsigned();
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->text('subtitle');

            $table->foreign('cart_discount_id')->on('cart_discounts')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_discounts');
        Schema::dropIfExists('cart_discount_translations');
    }
};
