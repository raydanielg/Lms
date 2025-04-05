<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('promotion_id');
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->text('description');

            $table->foreign('promotion_id')->on('promotions')->references('id')->onDelete('cascade');
        });

        Schema::table('promotions', function (Blueprint $table) {
           $table->dropColumn('title');
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
        Schema::dropIfExists('promotion_translations');
    }
}
