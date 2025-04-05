<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class EditColumnInOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            DB::statement("ALTER TABLE `order_items`
                MODIFY COLUMN `amount` double(15, 2) UNSIGNED NULL DEFAULT NULL AFTER `become_instructor_id`,
                MODIFY COLUMN `tax_price` double(15, 2) NULL DEFAULT NULL AFTER `tax`,
                MODIFY COLUMN `commission_price` double(15, 2) NULL DEFAULT NULL AFTER `commission`,
                MODIFY COLUMN `discount` double(15, 2) NULL DEFAULT NULL AFTER `commission_price`,
                MODIFY COLUMN `total_amount` double(15, 2) NULL DEFAULT NULL AFTER `discount`,
                MODIFY COLUMN `product_delivery_fee` double(15, 2) NULL DEFAULT NULL AFTER `total_amount`");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            //
        });
    }
}
