<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddProductDeliveryFeeColumnToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('product_delivery_fee', 13, 2)->nullable()->after('total_amount');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('product_delivery_fee', 13, 2)->nullable()->after('total_amount');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('product_delivery_fee', 13, 2)->nullable()->after('total_amount');
        });
    }
}
