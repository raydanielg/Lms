<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBundleWebinarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bundle_webinars', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('creator_id')->unsigned();
            $table->integer('bundle_id')->unsigned();
            $table->integer('webinar_id')->unsigned();
            $table->integer('order')->nullable()->unsigned();

            $table->foreign('bundle_id')->on('bundles')->references('id')->cascadeOnDelete();
            $table->foreign('webinar_id')->on('webinars')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bundle_webinars');
    }
}
