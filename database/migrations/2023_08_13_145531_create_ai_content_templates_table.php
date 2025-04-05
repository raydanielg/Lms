<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ai_content_templates', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->enum('type', ['text', 'image']);
            $table->boolean('enable_length')->default(false);
            $table->integer('length')->unsigned()->nullable();
            $table->enum('image_size', ['256', '512', '1024'])->nullable();
            $table->boolean('enable')->default(false);
            $table->bigInteger('created_at')->unsigned();
        });

        Schema::create('ai_content_template_translations', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('ai_content_template_id')->unsigned();
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->text('prompt')->nullable();

            $table->foreign('ai_content_template_id', 'ai_content_template_id_trans')->on('ai_content_templates')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ai_content_templates');
        Schema::dropIfExists('ai_content_template_translations');
    }
};
