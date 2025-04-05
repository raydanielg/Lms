<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestimonialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->string('user_name');
            $table->string('user_avatar');
            $table->string('user_bio');
            $table->string('rate', 5)->default('0');
            $table->text('comment');
            $table->enum('status', ['active', 'disable'])->default('disable');
            $table->integer('created_at')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('testimonials');
    }
}
