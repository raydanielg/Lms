<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\DB;

class AddNewStatusInReserveMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reserve_meetings', function (Blueprint $table) {
            DB::statement("ALTER TABLE `reserve_meetings` MODIFY COLUMN `status` enum('pending','open','finished','canceled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `password`");

            $table->integer('sale_id')->unsigned()->after('meeting_id')->nullable();
            $table->integer('date')->unsigned()->after('day');

            $table->foreign('sale_id')->on('sales')->references('id')->onDelete('cascade');
        });
    }
}
