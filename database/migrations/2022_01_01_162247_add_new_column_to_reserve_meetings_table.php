<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnToReserveMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reserve_meetings', function (Blueprint $table) {
            $table->enum('meeting_type', ['in_person', 'online'])->default('online')->after('paid_amount');
            $table->integer('student_count')->nullable()->after('meeting_type');
        });
    }
}
