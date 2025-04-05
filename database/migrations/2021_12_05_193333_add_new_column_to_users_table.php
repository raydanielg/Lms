<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddNewColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('country_id')->unsigned()->nullable()->after('address');
            $table->integer('province_id')->unsigned()->nullable()->after('country_id');
            $table->integer('city_id')->unsigned()->nullable()->after('province_id');
            $table->integer('district_id')->unsigned()->nullable()->after('city_id');
            $table->point('location')->nullable()->after('district_id');
            $table->boolean('group_meeting')->default(false)->after('location');
            $table->enum('meeting_type', ['all', 'in_person', 'online'])->default('all')->after('level_of_training');

            DB::statement("ALTER TABLE `users` ADD COLUMN `level_of_training` bit(3) NULL AFTER `location`");
        });
    }
}
