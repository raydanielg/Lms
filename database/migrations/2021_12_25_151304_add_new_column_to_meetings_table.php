<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnToMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->boolean('in_person')->default(false)->after('discount');
            $table->integer('in_person_amount')->nullable()->after('in_person');
            $table->boolean('group_meeting')->default(false)->after('in_person_amount');
            $table->integer('online_group_min_student')->nullable()->after('group_meeting');
            $table->integer('online_group_max_student')->nullable()->after('online_group_min_student');
            $table->integer('online_group_amount')->nullable()->after('online_group_max_student');
            $table->integer('in_person_group_min_student')->nullable()->after('online_group_amount');
            $table->integer('in_person_group_max_student')->nullable()->after('in_person_group_min_student');
            $table->integer('in_person_group_amount')->nullable()->after('in_person_group_max_student');
        });
    }
}
