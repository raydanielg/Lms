<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBundleFilterOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bundle_filter_option', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('bundle_id')->unsigned();
            $table->integer('filter_option_id')->unsigned();

            $table->foreign('bundle_id')->on('bundles')->references('id')->cascadeOnDelete();
            $table->foreign('filter_option_id')->on('filter_options')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bundle_filter_option');
    }
}
