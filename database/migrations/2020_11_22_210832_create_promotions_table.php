<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->string('title', 128);
            $table->integer('days')->unsigned();
            $table->integer('price')->unsigned();
            $table->string('icon');
            $table->boolean('is_popular')->default(false);
            $table->text('description');
            $table->integer('created_at')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotions');
    }
}
