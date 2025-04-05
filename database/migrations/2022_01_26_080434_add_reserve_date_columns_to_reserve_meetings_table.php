<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReserveDateColumnsToReserveMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reserve_meetings', function (Blueprint $table) {
            $table->bigInteger('start_at')->after('date')->unsigned();
            $table->bigInteger('end_at')->after('start_at')->unsigned();
        });
    }
}
