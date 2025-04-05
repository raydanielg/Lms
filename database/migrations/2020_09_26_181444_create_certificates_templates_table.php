<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificatesTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates_templates', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->string('image');
            $table->string('position_x',64)->nullable();
            $table->string('position_y',64)->nullable();
            $table->string('font_size',64)->nullable();
            $table->string('text_color',64)->nullable();
            $table->enum('status',['draft','publish'])->defult('draft');
            $table->integer('created_at');
            $table->integer('updated_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificates_templates');
    }
}
