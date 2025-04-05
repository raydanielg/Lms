<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraTimeToJoinToSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->integer('extra_time_to_join')->unsigned()->nullable()->after('link')->comment('Specifies that the user can see the join button up to a few minutes after the start time of the webinar.');
        });
    }
}
