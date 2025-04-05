<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_offers', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('creator_id')->unsigned();
            $table->integer('webinar_id')->unsigned();
            $table->string('name', 64)->nullable();
            $table->integer('percent')->unsigned();
            $table->enum('status', ['active', 'inactive']);
            $table->integer('created_at')->unsigned();
            $table->integer('from_date')->unsigned();
            $table->integer('to_date')->unsigned();

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('special_offers');
    }
}
