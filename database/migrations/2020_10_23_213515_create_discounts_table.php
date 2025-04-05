<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('creator_id')->unsigned();
            $table->string('name', 64)->unique();
            $table->integer('percent')->unsigned()->nullable();
            $table->integer('amount')->unsigned()->nullable();
            $table->integer('count')->default(1);
            $table->integer('created_at')->unsigned();
            $table->integer('expired_at')->unsigned();

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discounts');
    }
}
