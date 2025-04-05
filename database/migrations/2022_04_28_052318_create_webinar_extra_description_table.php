<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebinarExtraDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webinar_extra_descriptions', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('creator_id')->unsigned();
            $table->integer('webinar_id')->unsigned();
            $table->enum('type', \App\Models\WebinarExtraDescription::$types);
            $table->integer('order')->unsigned()->nullable();
            $table->bigInteger('created_at')->unsigned();

            $table->foreign('creator_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('webinar_id')->on('webinars')->references('id')->cascadeOnDelete();
        });

        Schema::create('webinar_extra_description_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('webinar_extra_description_id');
            $table->string('locale', 191)->index();
            $table->text('value');

            $table->foreign('webinar_extra_description_id', 'webinar_extra_description_id_foreign')->on('webinar_extra_descriptions')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webinar_extra_description');
        Schema::dropIfExists('webinar_extra_description_translations');
    }
}
