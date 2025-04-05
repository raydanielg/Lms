<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('blog_id');
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->text('description');
            $table->longText('content');
            $table->text('meta_description')->nullable();

            $table->unique(['blog_id', 'locale']);

            $table->foreign('blog_id')->references('id')->on('blog')->onDelete('cascade');
        });

        Schema::table('blog', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('description');
            $table->dropColumn('content');
            $table->dropColumn('meta_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog_translations');
    }
}
