<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('author_id')->unsigned();
            $table->string('title');
            $table->string('slug');
            $table->string('image');
            $table->text('description');
            $table->longText('content');
            $table->integer('visit_count')->unsigned()->nullable()->default(0);
            $table->boolean('enable_comment')->default(true);
            $table->enum('status', ['pending', 'publish'])->default('pending');
            $table->integer('created_at')->unsigned();
            $table->integer('updated_at')->nullable()->unsigned();

            $table->foreign('category_id')->references('id')->on('blog_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog');
    }
}
