<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_id')->unsigned();
            $table->enum('type', \App\Models\FormField::$fieldTypes);
            $table->integer('order')->nullable();
            $table->boolean('required')->default(false);

            $table->foreign('form_id')->on('forms')->references('id')->cascadeOnDelete();
        });

        Schema::create('form_field_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_field_id')->unsigned();
            $table->string('locale', 191)->index();
            $table->string('title');

            $table->foreign('form_field_id')->on('form_fields')->references('id')->cascadeOnDelete();
        });

        Schema::create('form_field_options', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_field_id')->unsigned();
            $table->integer('order')->nullable();

            $table->foreign('form_field_id')->on('form_fields')->references('id')->cascadeOnDelete();
        });

        Schema::create('form_field_option_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_field_option_id')->unsigned();
            $table->string('locale', 191)->index();
            $table->string('title');

            $table->foreign('form_field_option_id', 'form_field_option_id_trans')->on('form_field_options')->references('id')->cascadeOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_fields');
        Schema::dropIfExists('form_field_translations');
        Schema::dropIfExists('form_field_options');
        Schema::dropIfExists('form_field_option_translations');
    }
}
