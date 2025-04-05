<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangesInSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->integer('subscribe_id')->after('meeting_id')->unsigned()->nullable();
            DB::statement("ALTER TABLE `sales` MODIFY COLUMN `type` ENUM('webinar','meeting','subscribe') NOT NULL;");
            DB::statement("ALTER TABLE `sales` MODIFY COLUMN `payment_method` ENUM('credit','payment_channel','subscribe') NOT NULL;");
            DB::statement("ALTER TABLE `sales` CHANGE `seller_id` `seller_id` INTEGER UNSIGNED NULL;");
            DB::statement("ALTER TABLE `sales` CHANGE `order_id` `order_id` INTEGER UNSIGNED NULL;");

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
            $table->dropColumn('subscribe_id');
        });
    }
}
