<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnRoleToBecomeInstructorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('become_instructors', function (Blueprint $table) {
            $table->enum('role', ['teacher', 'organization'])->after('user_id');
            $table->integer('package_id')->unsigned()->nullable()->after('role');
        });
    }
}
