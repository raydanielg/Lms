<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForignKeyForRegionToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('country_id')->on('regions')->references('id')->nullOnDelete();
            $table->foreign('province_id')->on('regions')->references('id')->nullOnDelete();
            $table->foreign('city_id')->on('regions')->references('id')->nullOnDelete();
            $table->foreign('district_id')->on('regions')->references('id')->nullOnDelete();
        });
    }
}
