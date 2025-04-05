<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('country_id')->unsigned()->nullable();
            $table->integer('province_id')->unsigned()->nullable();
            $table->integer('city_id')->unsigned()->nullable();
            $table->point('geo_center')->nullable();
            $table->enum('type', ['country', 'province', 'city', 'district']);
            $table->string('title');
            $table->integer('created_at')->unsigned();
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->foreign('country_id')->on('regions')->references('id')->cascadeOnDelete();
            $table->foreign('province_id')->on('regions')->references('id')->cascadeOnDelete();
            $table->foreign('city_id')->on('regions')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('regions');
    }
}
