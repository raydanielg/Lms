<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeatureWebinarTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feature_webinar_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('feature_webinar_id');
            $table->string('locale', 191)->index();
            $table->text('description')->nullable();

            $table->foreign('feature_webinar_id')->on('feature_webinars')->references('id')->onDelete('cascade');
        });

        Schema::table('feature_webinars', function (Blueprint $table) {
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
        Schema::dropIfExists('feature_webinar_translations');
    }
}
