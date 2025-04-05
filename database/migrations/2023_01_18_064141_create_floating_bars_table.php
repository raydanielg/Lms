<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFloatingBarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('floating_bars', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('start_at')->nullable();
            $table->bigInteger('end_at')->nullable();
            $table->string('title_color')->nullable();
            $table->string('description_color')->nullable();
            $table->string('icon')->nullable();
            $table->string('background_color')->nullable();
            $table->string('background_image')->nullable();
            $table->string('btn_url')->nullable();
            $table->string('btn_color')->nullable();
            $table->string('btn_text_color')->nullable();
            $table->integer('bar_height')->nullable();
            $table->enum('position', ['top', 'bottom']);
            $table->boolean('fixed')->default(false);
            $table->boolean('enable')->default(false);
        });

        Schema::create('floating_bar_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('floating_bar_id')->unsigned();
            $table->string('locale', 191)->index();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('btn_text')->nullable();

            $table->foreign('floating_bar_id')->on('floating_bars')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('floating_bars');
        Schema::dropIfExists('floating_bar_translations');
    }
}
