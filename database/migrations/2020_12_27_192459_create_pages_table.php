<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->string('link')->unique()->index();
            $table->string('name');
            $table->string('title');
            $table->string('seo_description')->nullable();
            $table->boolean('robot')->default(false);
            $table->longText('content');
            $table->enum('status', ['publish', 'draft'])->default('draft');
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
        Schema::dropIfExists('pages');
    }
}
