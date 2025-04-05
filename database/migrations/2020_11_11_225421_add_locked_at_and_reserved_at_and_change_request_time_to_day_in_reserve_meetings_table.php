<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLockedAtAndReservedAtAndChangeRequestTimeToDayInReserveMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reserve_meetings', function (Blueprint $table) {
            $table->string('day',10)->after('meeting_time_id');
            $table->integer('locked_at')->nullable();
            $table->integer('reserved_at')->nullable();
            $table->dropColumn('request_time');
            DB::statement("ALTER TABLE `reserve_meetings` DROP FOREIGN KEY `reserve_meetings_meeting_id_foreign`;");
            $table->dropColumn('meeting_id');
            DB::statement("ALTER TABLE `reserve_meetings` MODIFY COLUMN  `status`  enum( 'pending', 'open', 'finished') NOT NULL ");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reserve_meetings', function (Blueprint $table) {
            $table->dropColumn('locked_at');
            $table->dropColumn('reserved_at');
            $table->integer('request_time');
            $table->dropColumn('day');
        });
    }
}
