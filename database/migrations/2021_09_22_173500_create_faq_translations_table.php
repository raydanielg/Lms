<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaqTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faq_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('faq_id');
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->text('answer');

            $table->foreign('faq_id')->on('faqs')->references('id')->onDelete('cascade');
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('answer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faq_translations');
    }
}
