<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeByerIdAndAddSellerIdInSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            DB::statement("ALTER TABLE `sales` DROP FOREIGN KEY `sales_user_id_foreign`;");
            DB::statement("ALTER TABLE `sales` CHANGE COLUMN  `user_id` `buyer_id` INTEGER UNSIGNED NOT NULL");
            $table->integer('seller_id')->unsigned()->after('id');

            $table->foreign('buyer_id')->on('users')->references('id')->onDelete('cascade');
            $table->foreign('seller_id')->on('users')->references('id')->onDelete('cascade');
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
            //
        });
    }
}
