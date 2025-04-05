<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('page_id');
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->string('seo_description')->nullable();
            $table->longText('content');

            $table->foreign('page_id')->on('pages')->references('id')->onDelete('cascade');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('seo_description');
            $table->dropColumn('content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_translations');
    }
}
