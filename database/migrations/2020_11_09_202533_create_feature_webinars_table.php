<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeatureWebinarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feature_webinars', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('webinar_id')->index()->unsigned();
            $table->enum('page', ['categories', 'home', 'home_categories']);
            $table->enum('status', ['publish', 'pending']);
            $table->integer('updated_at')->unsigned();

            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade')->onUpdate('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feature_webinars');
    }
}
