<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_orders', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->integer('seller_id')->unsigned();
            $table->integer('buyer_id')->unsigned();
            $table->integer('sale_id')->nullable()->unsigned();
            $table->text('specifications')->nullable();
            $table->integer('quantity')->unsigned();
            $table->integer('discount_id')->nullable()->unsigned();
            $table->text('message_to_seller')->nullable();
            $table->string('tracking_code')->nullable();
            $table->enum('status', \App\Models\ProductOrder::$status);
            $table->bigInteger('created_at')->unsigned();
        });

        Schema::table('cart', function (Blueprint $table) {
            $table->integer('product_order_id')->unsigned()->nullable()->after('webinar_id');
            $table->integer('product_discount_id')->unsigned()->nullable()->after('special_offer_id');

            $table->foreign('product_order_id')->on('product_orders')->references('id')->cascadeOnDelete();
            $table->foreign('product_discount_id')->on('product_discounts')->references('id')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_orders');
    }
}
