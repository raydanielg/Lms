<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gifts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('webinar_id')->unsigned()->nullable();
            $table->integer('bundle_id')->unsigned()->nullable();
            $table->integer('product_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('email');
            $table->bigInteger('date')->unsigned()->nullable();
            $table->text('description')->nullable();
            $table->boolean('viewed')->default(false)->comment('for show modal in recipient user panel');
            $table->enum('status', ['active', 'pending', 'cancel'])->default('pending');
            $table->bigInteger('created_at')->unsigned();

            $table->foreign('user_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('webinar_id')->on('webinars')->references('id')->cascadeOnDelete();
            $table->foreign('bundle_id')->on('bundles')->references('id')->cascadeOnDelete();
            $table->foreign('product_id')->on('products')->references('id')->cascadeOnDelete();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->integer('gift_id')->unsigned()->nullable()->after('installment_payment_id');
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `sales` MODIFY COLUMN `type` enum('webinar', 'meeting', 'subscribe', 'promotion', 'registration_package', 'product', 'bundle', 'installment_payment', 'gift') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `payment_method`");
        });

        Schema::table('cart', function (Blueprint $table) {
            $table->integer('gift_id')->unsigned()->nullable()->after('promotion_id');

            $table->foreign('gift_id')->on('gifts')->references('id')->cascadeOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('gift_id')->unsigned()->nullable()->after('promotion_id');

            $table->foreign('gift_id')->on('gifts')->references('id')->cascadeOnDelete();
        });

        Schema::table('accounting', function (Blueprint $table) {
            $table->integer('gift_id')->unsigned()->nullable()->after('installment_payment_id');
        });

        Schema::table('product_orders', function (Blueprint $table) {
            $table->integer('gift_id')->unsigned()->nullable()->after('installment_order_id');
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `product_orders` MODIFY COLUMN `buyer_id` int UNSIGNED NULL AFTER `seller_id`");

            $table->foreign('gift_id')->on('gifts')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gifts');
    }
}
