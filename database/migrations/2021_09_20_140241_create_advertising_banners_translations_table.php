<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertisingBannersTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertising_banners_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->unsignedInteger('advertising_banner_id');
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->string('image');

            $table->foreign('advertising_banner_id')->on('advertising_banners')->references('id')->onDelete('cascade');
        });

        Schema::table('advertising_banners', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advertising_banners_translations');
    }
}
