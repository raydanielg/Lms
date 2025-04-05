<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddProductIdToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->integer('product_order_id')->unsigned()->nullable()->after('promotion_id');

            DB::statement("ALTER TABLE `sales` MODIFY COLUMN `type` enum('webinar','meeting','subscribe','promotion','registration_package','product') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `registration_package_id`");
        });

        Schema::table('order_items', function (Blueprint $table) {
            //$table->integer('product_id')->unsigned()->nullable()->after('registration_package_id');
            $table->integer('product_order_id')->unsigned()->nullable()->after('product_id');
        });

        Schema::table('accounting', function (Blueprint $table) {
            $table->integer('product_id')->unsigned()->nullable()->after('registration_package_id');
        });
    }
}
