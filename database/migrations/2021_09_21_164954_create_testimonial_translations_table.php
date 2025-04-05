<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestimonialTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('testimonial_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('testimonial_id');
            $table->string('locale', 191)->index();
            $table->string('user_name');
            $table->string('user_bio');
            $table->text('comment');

            $table->foreign('testimonial_id')->on('testimonials')->references('id')->onDelete('cascade');
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn('user_name');
            $table->dropColumn('user_bio');
            $table->dropColumn('comment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('testimonial_translations');
    }
}
