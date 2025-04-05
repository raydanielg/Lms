<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebinarReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webinar_reports', function (Blueprint $table) {
            $table->engine='InnoDB';

            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('webinar_id')->unsigned();
            $table->string('reason');
            $table->text('message');
            $table->integer('created_at')->unsigned();

            $table->foreign('webinar_id')->on('webinars')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webinar_reports');
    }
}
