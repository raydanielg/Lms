<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebinarTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webinar_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('webinar_id');
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->text('seo_description')->nullable();
            $table->longText('description')->nullable();

            $table->foreign('webinar_id')->on('webinars')->references('id')->onDelete('cascade');
        });

        Schema::table('webinars', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('seo_description');
            $table->dropColumn('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webinar_translations');
    }
}
