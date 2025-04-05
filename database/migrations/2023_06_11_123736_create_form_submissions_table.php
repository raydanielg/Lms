<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('form_id')->unsigned();
            $table->bigInteger('created_at')->unsigned();

            $table->foreign('user_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('form_id')->on('forms')->references('id')->cascadeOnDelete();
        });

        Schema::create('form_submission_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('submission_id')->unsigned();
            $table->integer('form_field_id')->unsigned();
            $table->text('value')->nullable();

            $table->foreign('submission_id')->on('form_submissions')->references('id')->cascadeOnDelete();
            $table->foreign('form_field_id')->on('form_fields')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_submissions');
        Schema::dropIfExists('form_submission_items');
    }
}
