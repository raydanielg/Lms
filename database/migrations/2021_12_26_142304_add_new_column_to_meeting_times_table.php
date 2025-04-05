<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnToMeetingTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meeting_times', function (Blueprint $table) {
            $table->enum('meeting_type', ['all', 'in_person', 'online'])->default('all')->after('meeting_id');
        });
    }
}
