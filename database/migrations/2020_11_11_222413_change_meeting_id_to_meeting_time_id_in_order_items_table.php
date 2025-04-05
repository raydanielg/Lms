<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMeetingIdToMeetingTimeIdInOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            DB::statement("ALTER TABLE `order_items` DROP FOREIGN KEY `order_items_meeting_id_foreign`;");
            DB::statement("ALTER TABLE `order_items` CHANGE COLUMN  `meeting_id` `reserve_meeting_id` INTEGER UNSIGNED NULL");

            $table->foreign('reserve_meeting_id')->references('id')->on('reserve_meetings')->onDelete('cascade');
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
