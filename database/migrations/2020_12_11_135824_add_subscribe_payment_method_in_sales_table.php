<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubscribePaymentMethodInSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            DB::statement("ALTER TABLE `sales` MODIFY COLUMN `payment_method` ENUM('credit', 'payment_channel', 'subscribe')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            DB::statement("ALTER TABLE `sales` MODIFY COLUMN `payment_method` ENUM('credit', 'payment_channel', 'subscribe')");
        });
    }
}
