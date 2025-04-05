<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart', function (Blueprint $table) {
            DB::statement("ALTER TABLE `cart` MODIFY COLUMN `webinar_id` int(0) UNSIGNED NULL AFTER `creator_id`");

            $table->integer('reserve_meeting_id')->unsigned()->nullable()->after('webinar_id');
            $table->integer('subscribe_id')->unsigned()->nullable()->after('reserve_meeting_id');
            $table->integer('promotion_id')->unsigned()->nullable()->after('subscribe_id');

            $table->foreign('reserve_meeting_id')->on('reserve_meetings')->references('id')->onDelete('cascade');
            $table->foreign('subscribe_id')->on('subscribes')->references('id')->onDelete('cascade');
            $table->foreign('promotion_id')->on('promotions')->references('id')->onDelete('cascade');
        });
    }

}
